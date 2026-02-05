<?php
header("Content-Type: application/json");
require_once "../config/mongodb.php";

use MongoDB\BSON\UTCDateTime;

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["uid"])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid data"]);
    exit;
}

$donorsCollection->updateOne(
    ["uid" => $data["uid"]],
    [
        '$set' => [
            "uid"           => $data["uid"],
            "name"          => $data["name"],
            "email"         => $data["email"],
            "blood_group"   => $data["blood_group"],
            "city"          => $data["city"],
            "mobile"        => $data["mobile"] ?? "",
            "available"     => $data["available"],
            "last_donation" => $data["last_donation"] ?? null,
            "updated_at"    => new UTCDateTime()
        ]
    ],
    ["upsert" => true]
);

echo json_encode(["success" => true]);
