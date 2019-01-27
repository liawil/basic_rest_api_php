<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
// database connection will be here
// include database and object files
include_once '../config/database.php';

// instantiate database and entity object
$db = new Database();
$db->open();

$table = (isset($_GET['table']) && !empty($_GET['table']))? $_GET['table']: '';
if ($table == "" OR !$db->existTable($table)) {
    die("Error: Not table found !");
}
$db->close();
try {
    include_once '../models/'.$table.'.php';    
} catch (Exception $e) {
    die("Error: ".$e->getMessage());
}

// Uppercase First letter to obtain the classname
$class = ucwords($table);

// initialize object
$entity = new $class($db);

// set ID property of record to read
$entity->id = isset($_GET['id']) ? intval($_GET['id']) : die();


// read the details of product to be edited
$stmt = $entity->show();
 
if($stmt->rowCount()){

    $entity_arr = array();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    foreach ($row as $key => $value) {
        $entity_arr[$key] = html_entity_decode($value);
    }
     
    // set response code - 200 OK
    http_response_code(200);
 
    // make it json format
    echo json_encode($entity_arr);
}
 
else{
    // set response code - 404 Not found
    http_response_code(404);
 
    // tell the user product does not exist
    echo json_encode(array("message" => "Product does not exist."));
}
?>