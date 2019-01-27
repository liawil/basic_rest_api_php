<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// database connection will be here
include_once '../config/database.php';

// instantiate database and entity object
$db = new Database();
$db->open();

$table = (isset($_GET['table']) && !empty($_GET['table']))? $_GET['table']: '';
if ($table == "" OR !$db->existTable($table)) {
    die("Error: Not table found !");
}

// include database and object files
try {
    include_once '../models/'.$table.'.php';    
} catch (Exception $e) {
    die("Error: ".$e->getMessage());
}

// Uppercase First letter to obtain the classname
$class = ucwords($table);

// initialize object
$entity = new $class($db);
 
// get id of table to be edited
$data = json_decode(file_get_contents("php://input"));

// set product id to be deleted
    //$entity->id = $data->id;
    $entity->id = isset($_GET['id']) ? intval($_GET['id']) : die();

//echo json_encode($entity);exit();
 
// delete the entity
if($entity->delete()){
 
    // set response code - 200 ok
    http_response_code(200);
 
    // tell the user
    echo json_encode(array("message" => "$table was deleted."));
}
 
// if unable to delete the entity
else{
 
    // set response code - 503 service unavailable
    http_response_code(503);
 
    // tell the user
    echo json_encode(array("message" => "Unable to delete $table."));
}
?>