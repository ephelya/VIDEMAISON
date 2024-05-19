<?php
namespace Models;
    use \Models\Admin;
    use Utils\Forms;

class Dreams {

        public function __construct($data) {
        //print_r($data);
         foreach ($data as $key => $value) {//on envie un membre
                $this->$key = $value;
            }
        }
       
        public static function getDream($id)
        { //echo "session ".$_SESSION['userId'];
            $pdo = \Models\Admin::db();
            $query = "SELECT * FROM dreamsUser WHERE id = $id"; //echo $query;
            $stmt = $pdo->prepare('SELECT * FROM Membres WHERE idMembre = :userId');
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            $dream = $stmt->fetch(\PDO::FETCH_OBJ);
             return new Dreams($dream);
        }

        public function getUserDreams ($userId)
        {
          //  echo "fct $userId";
            $dreams = array();
            $pdo = \Models\Admin::db();
            $sql = "SELECT * FROM dreamsUser WHERE userId = $userId";
            //echo $sql;//
            $sql = "SELECT * FROM dreamsUser WHERE userId = :userId";
            $stmt = $pdo->prepare($sql);  
            $stmt->bindParam(':userId', $userId, \PDO::PARAM_STR);
            $stmt->execute();
            $dreams = $stmt->fetch(\PDO::FETCH_OBJ);
            if ($dreams) { $dreams = new Dreams($dreams);  return $dreams;}
           else { return array();}
        }

        public static function getDreamsForm() {

            $dreamsformConfig = [
                'formId'   =>  'form_dreamsform',
                'method' => "post",
                'size' => "large",
                'float' => 1,
                'label' => "down",
                'trait' => "#",
                'fields' => [
                    ["id" => "valid_dreamsform", 'name' => "valid_dreamsform", 'type' => 'hidden'],
                    ['label' => 'Titre', "id" => "name1", 'name' => "name_1", 'type' => 'text', 'placeholder' => "Donnez un titre à votre rêve"],
                    ['label' => 'Titre', "id" => "name2", 'name' => "name_2", 'type' => 'text', 'placeholder' => "Donnez un titre à votre rêve"],
                    ['label' => 'Titre', "id" => "name3", 'name' => "name_3", 'type' => 'text', 'placeholder' => "Donnez un titre à votre rêve"],
                    ["value" => "Prev", 'class' => 'prev',  'data' =>'data-action =\'prev\'', 'type' => 'submit'],
                    ["value" => "Suivant", 'class' => 'validdreamsdetails', 'data' =>'data-action =\'next\'', 'type' => 'button'],
                ]
            ];
            return Forms::generateForm($dreamsformConfig);
        }

        public  function getDreamsDetails($id) {
            //print_r($_SESSION);
            //print_r($this -> dreamsTitles);
            $key = array_search($id, $this -> dreamsId); //echo "key $key";

            if (isset($this -> dreamsTitles[$key])) {$dreamtitle = $this -> dreamsTitles[$key]; } else { $dreamtitle=""; }
                $dreamsformConfig = [
                    'formId'   =>  "form_dreamsdetails",
                    'dreamtitle' => $dreamtitle,
                    'method' => "enctype",
                    'size' => "large",
                    'float' => 1,
                    'label' => "down",
                    'trait' => "#",
                    'fields' => [
                        ['name' => "valid_dreamsdetail", 'type' => 'hidden', "value" => $id],
                        ['label' => 'Rêve / objectif', "id" => "details", 'name' => "details", 'msg' => '', 'type' => 'textarea', "required" => 1],
                        ['label' => 'Photo de référence (facultatif)', "id" => "photoass", 'name' => "photoass", 'type' => 'file', "required" => 0],
                        ["value" => "Prev", 'class' => 'prev',  'data' =>'data-action =\'prev\'', 'type' => 'submit'],
                        ["value" => "Ajouter", 'class' => 'validdreamsdetails', 'data' =>'data-action =\'next\'', 'type' => 'submit'],
                    ]
                ];
        
                return Forms::generateForm($dreamsformConfig);
        }

        
    

    }

  


