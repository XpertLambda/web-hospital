<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../objects/nurse.php';

$database = new Database();
$db = $database->getConnection();
 
$nurse = new Nurse($db);
$stmt = $nurse->read();

$num = $stmt->rowCount();
if($num>0){
    $nurses_arr = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $nurse_item = array(
            "id" => $id,
            "name" => $name,
            "email" => $email,
            "phone" => $phone,
            "created" => $created
        );
        array_push($nurses_arr, $nurse_item);
    }
    echo json_encode($nurses_arr);
}
else{
    echo json_encode(array());
}
