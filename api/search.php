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
$stmt = $entity->search($keywords);
$num = $stmt->rowCount();
 
// check if more than 0 record found
if($num>0){
 
    // products array
    $entity_arr=array();
    $entity_arr["records"]=array();
    $entity_arr["paging_site"]=array();
    $entity_arr["paging_api"]=array();
 
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $entity_item = array();

        foreach ($row as $key => $value) {
            if ($key == "id_".$table) $id = $value;
            $entity_item[$key] = html_entity_decode($value);
        }
        $entity_item['link_api_url'] = "{$api_url}show/{$table}/{$id}";
        $entity_item['link_site_url'] = "{$home_url}{$table}&id={$id}";
        array_push($entity_arr["records"], $entity_item);
    }

    // include paging
    $total_rows=$num;
    $page_url="{$home_url}{$table}&search={$keywords}&";
    $page_api_url="{$api_url}search/{$table}/{$keywords}?";

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
    // set response code - 404 Not found
    //http_response_code(404);
 
    // tell the user no products found
    echo json_encode(
        array("message" => "No $table found.")
    );
}
?>