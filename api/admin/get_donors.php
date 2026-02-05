<?php
require_once "../../config/mongodb.php";

$cursor = $donorsCollection->find(
    [],
    ["sort" => ["created_at" => -1]] // newest first
);

$data = [];

foreach ($cursor as $d) {
    $data[] = [
        "id" => (string)$d["_id"],
        "name" => $d["name"] ?? "",
        "blood_group" => $d["blood_group"] ?? "",
        "city" => $d["city"] ?? "",
        "mobile" => $d["mobile"] ?? "",
        "available" => $d["available"] ?? true
    ];
}

echo json_encode($data);
