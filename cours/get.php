<?php

use Taf\TafAuth;
use Taf\TableQuery;

try {
    require './config.php';
    require '../TableQuery.php';
    require '../taf_auth/TafAuth.php';
    $taf_auth = new TafAuth();
    /* 
        $params
        contient tous les parametres envoyés par la methode POST
     */
    // toutes les actions nécéssitent une authentification
    $auth_reponse=$taf_auth->check_auth();
    if ($auth_reponse["status"] == false && count($params)==0) {
        echo json_encode($auth_reponse);
        die;
    }
    
    $table_query=new TableQuery($table_name);

    $condition=$table_query->dynamicCondition($params,"=");
    // $reponse["condition"]=$condition;
    $query="select *from $table_name ".$condition;
    $reponse["data"] = $taf_config->get_db()->query(
        " SELECT c.*,m.*,n.*
        FROM cours c, module m, niveau n
        WHERE c.id_module=m.id_module
        AND c.id_niveau=n.id_niveau
        
        "
    )->fetchAll(PDO::FETCH_ASSOC);
    $reponse["status"] = true;

    echo json_encode($reponse);
} catch (\Throwable $th) {
    $reponse["status"] = false;
    $reponse["erreur"] = $th->getMessage();

    echo json_encode($reponse);
}

?>