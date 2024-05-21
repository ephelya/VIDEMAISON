<?php
// routes.php

//session_start();
$_SESSION["userId"] = 445;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;

require_once __DIR__ . '/../vendor/autoload.php'; // Chemin vers le fichier autoload de Composer

error_log(date("d/m/y H:i")." chargement de la page\n");

// Fonction pour rendre une page avec des données
function renderPage(Request $request, Response $response, $args, $twig, $pageData) {
    $page = $pageData["page"];
    error_log(date("d/m/y H:i")." render $page\n");

    $baseData = \Models\Pages::baseData($page); 
    $combinedData = array_merge($baseData, $pageData);

    $model = $combinedData["twig"];
    return $twig->render($response, $model, $combinedData);
}

// Route pour la page d'accueil
$app->get('/', function (Request $request, Response $response, $args) use ($twig) {
    $homeController = new \Controllers\HomeController();
    $pageData = $homeController->getHomePageData();
    error_log(date("d/m/y H:i")." chargement de la page accueil\n");

    return renderPage($request, $response, $args, $twig, $pageData);
});

// Route pour la page d'admin
$app->get('/admin[/{rubrique}]', function (Request $request, Response $response, $args) use ($twig) {
    error_log(date("d/m/y H:i")."Accès admin\n");

    $rubrique = $args['rubrique'] ?? 'default'; 
 
    $controller = new \Controllers\AdminController();
    $function = "get" . ucfirst($rubrique) . "Data";

    if (!method_exists($controller, $function)) {
        $function = 'getDefaultData';
    }

    $pageData = $controller->$function();
    return renderPage($request, $response, $args, $twig, $pageData);
});

// Route générique pour les autres pages
$app->get('/{page}', function (Request $request, Response $response, $args) use ($twig) {
    $pageIdent = $args['page'];
    error_log(date("d/m/y H:i")."Accès page GET\n");

    $page = ucfirst($pageIdent) . "Controller";
    $controllerClass = "\\Controllers\\" . $page;
    if (class_exists($controllerClass)) {
        $controller = new $controllerClass();
        if (method_exists($controller, 'getPageData')) {
            $pageData = $controller->getPageData($pageIdent);
            $otherData = $controller->getPageData("page");
            foreach ($otherData as $key => $value) {
                $pageData[$key] = $value;
            }
            $pageData["page"] = $pageIdent;
            return renderPage($request, $response, $args, $twig, $pageData);
        } else {
            throw new HttpNotFoundException($request, "Méthode getPageData non trouvée pour le contrôleur " . $controllerClass);
        }
    } elseif (class_exists('\\Controllers\\PageController')) {
        $controller = new \Controllers\PageController();
        if (method_exists($controller, 'getPageData')) {
            $pageData = $controller->getPageData($page);
            $otherData = $controller->getPageData($pageIdent);
            foreach ($otherData as $key => $value) {
                $pageData[$key] = $value;
            }
            $pageData["page"] = $pageIdent;
            return renderPage($request, $response, $args, $twig, $pageData);
        } else {
            throw new HttpNotFoundException($request, "Méthode getPageData non trouvée pour le contrôleur PageController");
        }
    } else {
        throw new HttpNotFoundException($request, "Contrôleur " . $controllerClass . " non trouvé");
    }
});

// Route pour gérer les API updates
$app->post('/api/update', function ($request, $response, $args) {
    error_log(date("d/m/y H:i")."API update");

    \Models\Produit::imagesTransfer();

    $responseData = [
        'success' => true,
        'message' => 'Images transferred successfully'
    ];

    $response->getBody()->write(json_encode($responseData));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

// Route générique pour les API
$app->post('/api/{api}', function (Request $request, Response $response, $args) use ($twig) {
    $api = $args['api'];
    error_log("apiValue env: $api " . print_r($_POST, true)."\n");

    return \Controllers\ApiController::getApi($api, $request, $response, $args);
});

$app->get('/api/{api}', function (Request $request, Response $response, $args) use ($twig) {
    $api = $args['api'];

    $apictrl = new \Controllers\ApiController();
    $response = $apictrl->getApi($api);
    return $response;
});

// Route pour servir les fichiers dans le répertoire UPLOADS
$app->get('/UPLOADS/{filename:.*}', function ($request, $response, $args) {
    $filePath = __DIR__ . '/../UPLOADS/' . $args['filename'];
    error_log("Trying to serve file: " . $filePath); // Log pour débogage
    if (file_exists($filePath)) {
        error_log("File exists: " . $filePath); // Log pour débogage

        $fileStream = fopen($filePath, 'rb');
        $response = $response->withHeader('Content-Type', mime_content_type($filePath));
        $response->getBody()->write(stream_get_contents($fileStream));
        fclose($fileStream);

        return $response;
    }
    error_log("File not found: " . $filePath); // Log pour débogage
    return $response->withStatus(404, 'File not found');
});



// Route pour gérer les URL non trouvées
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
    throw new HttpNotFoundException($request);
});

?>