<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 0");
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
(array)$data = json_decode(file_get_contents("php://input"));
 
// make sure data is not empty
$data_has_empty_value = false;
if (!count((array)$data)){
    $data_has_empty_value = true;
}else{ 
// set ID property of table to be edited
	//$entity->id = $data->id;
    $entity->id = isset($_GET['id']) ? intval($_GET['id']) : die();

// set entity property values
    foreach ($data as $key => $value) {
        if (empty($value)) {
            $data_has_empty_value = true;
            break;
        }
        else if($key != 'id'){
        	$entity->update_values[$key] = $value;
        }
    }
}
$stmt = $entity->update();
$response = $stmt->fetch(PDO::FETCH_ASSOC);

// update the table
if(!$data_has_empty_value && $response){
 
    // set response code - 200 ok
    http_response_code(200);
 	
    // tell the user
    echo json_encode(array("message" => "$table was updated."));
}
 
// if unable to update the table, tell the user
else{
 
    // set response code - 503 service unavailable
    http_response_code(503);
 
    // tell the user
    echo json_encode(array("message" => "Unable to update $table."));
}