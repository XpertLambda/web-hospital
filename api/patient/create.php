<?php
include_once '../config/database.php';
include_once '../objects/patient.php';

$database = new Database();
$db = $database->getConnection();
 
$patient = new Patient($db);

$patient->name = $_POST['name'];
$patient->phone = $_POST['phone'];
$patient->gender = $_POST['gender'];
$patient->health_condition = $_POST['health_condition'];
$patient->doctor_id = $_POST['doctor_id'];
$patient->nurse_id = $_POST['nurse_id'];

if($patient->create()){
    $patient_arr = array(
        "status" => true,
        "message" => "Successfully Added!",
        "id" => $patient->id
    );
}
else{
    $patient_arr = array(
        "status" => false,
        "message" => "Failed to create patient!"
    );
}
print_r(json_encode($patient_arr));
