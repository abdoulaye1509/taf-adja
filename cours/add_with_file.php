<?php

use Taf\TafAuth;
use Taf\TableQuery;

try {
    require './config.php';
    require '../TableQuery.php';
    require '../taf_auth/TafAuth.php';
    $taf_auth = new TafAuth();
    // toutes les actions nécéssitent une authentification
    $auth_reponse = $taf_auth->check_auth();
    if ($auth_reponse["status"] == false) {
        echo json_encode($auth_reponse);
        die;
    }

    $table_query = new TableQuery($table_name);
    /* 
        $params
        contient tous les parametres envoyés par la methode POST
     */

    $params_data = $_POST;
    if (empty($params_data)) {
        $reponse["status"] = false;
        $reponse["erreur"] = "Parameters required";
        // $reponse["params_data"] = $params_data;
        echo json_encode($reponse,JSON_INVALID_UTF8_IGNORE);
        exit;
    }
    // chargement des fichiers
    $form_files = json_decode(json_encode($_FILES), true);
    umask(022);
    foreach ($form_files as $key => $one_form_control) {
        $nombre_fichiers = count($one_form_control["name"]);
        for ($i = 0; $i < $nombre_fichiers; $i++) {
            // Obtention de l'extension
            $file_size = $one_form_control["size"][$i];
            $file_name = $one_form_control["name"][$i];
            $timestampActuel = time();
            $new_file_name = $timestampActuel . "_" . $key . "_" . $file_name;
            $file_tmp_name = $one_form_control["tmp_name"][$i];
            $extension = substr($file_name, strrpos($file_name, '.') + 1);
            $file_path = $_SERVER['DOCUMENT_ROOT'] . "/taf-adja/cours/poncl-files/" . basename($new_file_name);
            if ($file_size < 100000000 && in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'mp4', 'avi', 'mp3'], true) && move_uploaded_file($file_tmp_name, $file_path)) { // fichier autorisé
                // modification des données de type fichier à enregistrer
                $params_data[$key]=$new_file_name;
                $reponse["fichier"][$key][] = ["status" => true];
            } else {
                $params_data[$key]=null;
                $reponse["fichier"][$key][] = ["status" => false, "message" => "fichier trop lourd ou extension non autoritée"];
            }

        }
    }
    // pour charger l'heure courante
    // $params["date_enregistrement"]=date("Y-m-d H:i:s");
    $query = $table_query->dynamicInsert2($params_data);
    // $reponse["query"]=$query;
    if ($taf_config->get_db()->exec($query)) {
        
        $reponse["status"] = true;
        $params_data["id_$table_name"] = $taf_config->get_db()->lastInsertId();
        $reponse["data"] = $params_data;
        $reponse["form_files"] = $form_files;
    } else {
        $reponse["status"] = false;
        $reponse["erreur"] = "Erreur d'insertion à la base de ";
    }
    echo json_encode($reponse,JSON_INVALID_UTF8_IGNORE);
} catch (\Throwable $th) {

    $reponse["status"] = false;
    $reponse["erreur"] = $th->getMessage();

    echo json_encode($reponse,JSON_INVALID_UTF8_IGNORE);
}
