<?php
require_once "../../config/mongodb.php";

$data = json_decode(file_get_contents("php://input"), true);

$donorsCollection->insertOne([
    "name" => $data["name"],
    "blood_group" => $data["blood_group"],
    "city" => $data["city"],
    "mobile" => $data["mobile"],
    "available" => true,
    "created_at" => new MongoDB\BSON\UTCDateTime()
]);

echo json_encode(["success" => true]);
