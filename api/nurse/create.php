<?php
include_once '../config/database.php';
include_once '../objects/nurse.php';

$database = new Database();
$db = $database->getConnection();
 
$nurse = new Nurse($db);

$nurse->name = $_POST['name'];
$nurse->email = $_POST['email'];
$nurse->password = base64_encode($_POST['password']);
$nurse->phone = $_POST['phone'];

if($nurse->create()){
    $nurse_arr = array(
        "status" => true,
        "message" => "Successfully Added!",
        "id" => $nurse->id,
        "name" => $nurse->name
    );
}
else{
    $nurse_arr = array(
        "status" => false,
        "message" => "Email already exists!"
    );
}
print_r(json_encode($nurse_arr));
