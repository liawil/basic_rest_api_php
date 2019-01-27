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
 
// get posted data
$data = json_decode(file_get_contents("php://input"));

$data_has_empty_value = false;
$data = (array) $data;

// make sure data is not empty
if (!count($data)){
    $data_has_empty_value = true;
}else{
    foreach ($data as $key => $value) {
        if (empty($value)) {
            //echo "data has a empty value";
            $data_has_empty_value = true;
            break;
        }
    }
}

if(!$data_has_empty_value){
    
    // set entity property values
    foreach ($data as $key => $value) {
        $entity->$key = $value;
    }
    $entity->setData();

    $stmt = $entity->create();
$response = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($response); exit();
 
    // create the entity
    if($entity->create()){
 
        // set response code - 201 created
        http_response_code(201);
 
        // tell the user
        echo json_encode(array("message" => $table." was created."));
    }
 
    // if unable to create the table, tell the user
    else{
 
        // set response code - 503 service unavailable
        http_response_code(503);
 
        // tell the user
        echo json_encode(array("message" => "Unable to create ".$table."."));
    }
}
 
// tell the user data is incomplete
else{
 
    // set response code - 400 bad request
    //http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Unable to create ".$table.". Data is incomplete."));
}