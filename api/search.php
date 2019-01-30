<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// database connection will be here
include_once '../config/database.php';

// instantiate database and product object
$db = new Database();
$db->open();

$table = (isset($_GET['table']) && !empty($_GET['table']))? $_GET['table']: '';
if ($table == "" OR !$db->existTable($table)) {
    http_response_code(404);
    die("Error: Not table found !");
}

// include object files
include_once '../models/'.$table.'.php';
include_once '../config/core.php';
include_once '../shared/utilities.php';

$utilities = new Utilities();

// Uppercase First letter to obtain the classname
$class = ucwords($table);

// initialize object
$entity = new $class($db);
 
// get keywords
$keywords=isset($_GET["s"]) ? $_GET["s"] : "";
 
// query products
$stmt = (isset($_GET['page']))? $entity->search($keywords, intval($from_record_num)) : $entity->search($keywords);
$num = $stmt->rowCount();
 
// check if more than 0 record found
if($num>0){
 
    // products array
    $entity_arr=array();
    $entity_arr["records"]= array();
    $entity_arr["paging_site"]=array();
    $entity_arr["paging_api"]=array();
 
    //$entity_arr["records"] = buildResponse($stmt,$table,$db);
    $entity_arr["records"] = json_decode("[".buildResponse($stmt,$table,$db)."]");

    // include paging
    $total_rows=$entity->count();
    $page_url="{$home_url}{$table}&search={$keywords}&";
    $page_api_url="{$api_url}search/{$table}/{$keywords}/";

    $paging_site=$utilities->getPaging($page, $total_rows, $records_per_page, $page_url);
    $entity_arr["paging_site"]=$paging_site;

    $paging_api=$utilities->getPaging($page, $total_rows, $records_per_page, $page_api_url);
    $entity_arr["paging_api"]=$paging_api;
 
    // set response code - 200 OK
    http_response_code(200);
 
    // show products data
    echo json_encode($entity_arr);
}
 
else{
    // set response code - 204 No content
    http_response_code(204);
 
    // tell the user no entity found
    echo json_encode(
        array("message" => "No ".$table." found.")
    );
}