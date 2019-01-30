<?php
// affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);
 
// api url
$api_url= $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME']."/msd4a/api/";
// home url de l'application
$home_url="http://localhost/d4a/?route=";
 
// si une page a été mentionné dans les paramettre de l'url. par défaut c'est 1
$page = isset($_GET['page']) ? $_GET['page'] : 1;
 
// nombre d'enregistrement par page. Pour le système de pagination
$records_per_page = 4;
 
// calcule pour la clause LIMIT de la requête pour le système de pagination
$from_record_num = ($records_per_page * $page) - $records_per_page;
