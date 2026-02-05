<?php
require_once "../../config/mongodb.php";

$data = json_decode(file_get_contents("php://input"), true);

$donorsCollection->deleteOne([
    "_id" => new MongoDB\BSON\ObjectId($data["id"])
]);

echo json_encode(["success" => true]);
