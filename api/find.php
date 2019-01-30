<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// database connection will be here
include_once '../config/database.php';

// instantiate database and product object
$db = new Database();
$db->open();

$table = (isset($_GET['table']) && !empty($_GET['table']))? $_GET['table']: '';
if ($table == "" OR !$db->existTable($table)) {
    http_response_code(404);
    die(json_encode(
        array("message" => "Table ".$table." not found !")
    ));
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

// get ID
if(isset($_GET['id']))
    $id_find = intval($_GET['id']) ;
else{
    // set response code - 409 Bad request
    http_response_code(409);

    die(json_encode(
        array("message" => "ID doesn't given !")
    ));
} 
 
// read products will be here
// query products

$find = (isset($_GET['find']) && !empty($_GET['find']))? "find".$_GET['find']: 'find';

//check if method given exists in the current class
if(!method_exists($entity, $find)){
    // set response code - 404 No content
    http_response_code(404);
 
    // tell the user no entity found
    die(json_encode(
        array("message" => $find." no found for ".$table)
    ));
}

$stmt = (isset($_GET['page']))? $entity->$find($id_find, intval($from_record_num)) : $entity->$find($id_find);
$num = $stmt->rowCount();

 
// check if more than 0 record found
if($num>0){
 
    // products array
    $entity_arr=array();
    $entity_arr["records"]=array();
    $entity_arr["paging_site"]=array();
    $entity_arr["paging_api"]=array();
 
    //$entity_arr["records"] = buildResponse($stmt,$table,$db);
    $entity_arr["records"] = json_decode("[".buildResponse($stmt,$table,$db)."]");

    // include paging
    $total_rows=$entity->count();
    $page_url="{$home_url}{$table}&find={$find}&id={$id_find}&";
    $page_api_url="{$api_url}{$find}/{$table}/{$id_find}/";

    $paging_site=$utilities->getPaging($page, $total_rows, $records_per_page, $page_url);
    $entity_arr["paging_site"]=$paging_site;

    $paging_api=$utilities->getPaging($page, $total_rows, $records_per_page, $page_api_url);
    $entity_arr["paging_api"]=$paging_api;
 
    // set response code - 200 OK
    http_response_code(200);
 
    // show products data in json format
    echo json_encode($entity_arr);
} 
// no products found will be here
else{
 
    // set response code - 204 No content
    http_response_code(204);
 
    // tell the user no entity found
    echo json_encode(
        array("message" => "No ".$table." found.")
    );
}