<?php
// Fichier: bootstrap.php

use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
// Load config
$development = false;
require_once __DIR__ . '/config.php';

// Create app
$app = AppFactory::create();

// Middleware de gestion des erreurs
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler(function ($request, $exception, $displayErrorDetails, $logErrors, $logErrorDetails) {
    error_log($exception->getMessage()); // Enregistre l'erreur dans le fichier de log PHP
    $response = new \Slim\Psr7\Response();
    $response->getBody()->write('Erreur interne du serveur.');
    return $response->withStatus(500);
});

// Ajouter le middleware de routage
$app->addRoutingMiddleware();

// Ajouter le middleware de gestion des erreurs
$errorMiddleware = $app->addErrorMiddleware($development, $development, $development);

// Create Twig
$twigOptions = $development ? ['cache' => false] : ['cache' => __DIR__ . '/../cache'];
$twig = Twig::create(__DIR__ . '/views', $twigOptions);

// Add Twig-View Middleware
$app->add(TwigMiddleware::create($app, $twig));

// Load our routes
require_once __DIR__ . '/routes.php';

// Exécuter l'application
$app->run();
