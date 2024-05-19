<?php
namespace Models;

class Menus {
    public $id;
    public $name; // Assumant que vous avez une propriété 'name'. Ajustez selon votre schéma de base de données.

    // Constructeur pour initialiser les propriétés de l'objet
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    // Méthode pour récupérer les menus et créer des objets Menus
    public static function getMenus($pageName)
    {
        $pdo = Admin::db(); // Assurez-vous que cette méthode retourne une instance de PDO
        $sql =  "SELECT nm.id, nm.name FROM navigation_menus nm
        JOIN pageMenus pm ON nm.id = pm.menuId
        JOIN Pages p ON p.id = pm.pageId
        WHERE p.name = '$pageName'"; //echo $sql;
        $sql = "SELECT nm.id, nm.name FROM navigation_menus nm
                JOIN pageMenus pm ON nm.id = pm.menuId
                JOIN Pages p ON p.id = pm.pageId
                WHERE p.name = :name";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $pageName, \PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $menus = [];
        foreach ($results as $row) {
            $menus[] = new Menus($row['id'], $row['name']);
        }

        return $menus;
    }

    public static function getMenusLinks($menuName)
    {
        $pdo = Admin::db(); // Assurez-vous que cette méthode retourne une instance de PDO
        // Récupère tous les menus et les liens associés à chaque menu
        $sql = "SELECT nm.id as menuId, nm.name as menuName, nl.id as linkId, nl.link_url, nl.link_value, nl.link_ident 
                FROM navigation_menus nm
                LEFT JOIN menusLinks ml ON nm.id = ml.menuId
                LEFT JOIN navigation_links nl ON ml.linkId = nl.id
                WHERE nm.name = '$menuName'
                ORDER BY nm.id, nl.id"; //echo $sql;
        $sql = "SELECT nm.id as menuId, nm.name as menuName, nl.id as linkId, nl.link_url, nl.link_value, nl.link_ident 
                FROM navigation_menus nm
                LEFT JOIN menusLinks ml ON nm.id = ml.menuId
                LEFT JOIN navigation_links nl ON ml.linkId = nl.id
                WHERE  nm.name = :menuName
                ORDER BY nm.id, nl.id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':menuName', $menuName, \PDO::PARAM_STR);

        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC); //
        foreach($results as $menu)
        {
            $value = \Models\Admin::loadTranslationByKey($menu ["link_ident"], $_SESSION['lang']) ;// echo $menu ["link_ident"]." ".$value;
            $link = str_replace("#", HOME, $menu ["link_url"]);
            $menus[] = [ "url" => $link, "value" =>$value, "ident" =>$menu ["link_ident"]]; 
        }
        //print_r($menus);

        return $menus;
    }
}
?>