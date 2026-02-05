<?php
require_once "../../config/mongodb.php";

$data = json_decode(file_get_contents("php://input"), true);

$donorsCollection->updateOne(
    ["_id" => new MongoDB\BSON\ObjectId($data["id"])],
    [
        '$set' => [
            "name" => $data["name"],
            "blood_group" => $data["blood_group"],
            "city" => $data["city"],
            "mobile" => $data["mobile"],
            "available" => $data["available"]
        ]
    ]
);

echo json_encode(["success" => true]);
