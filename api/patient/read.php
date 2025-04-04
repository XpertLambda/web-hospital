<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../objects/patient.php';

$database = new Database();
$db = $database->getConnection();
 
$patient = new Patient($db);
$stmt = $patient->read();
$num = $stmt->rowCount();

if($num>0){
    $patients_arr = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $patient_item = array(
            "id" => $id,
            "name" => $name,
            "phone" => $phone,
            "gender" => $gender,
            "health_condition" => $health_condition,
            "doctor_name" => $doctor_name,
            "nurse_name" => $nurse_name
        );
        array_push($patients_arr, $patient_item);
    }
    echo json_encode($patients_arr);
}
else{
    echo json_encode(array());
}
