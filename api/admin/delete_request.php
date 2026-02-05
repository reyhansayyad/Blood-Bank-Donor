<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/mongodb.php";

$raw = file_get_contents("php://input");
$data = $raw ? json_decode($raw, true) : null;

if (!$data || empty($data["id"])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit;
}

$idStr = trim((string) $data["id"]);

try {
    $requestsCollection = $db->blood_requests;
    $result = $requestsCollection->deleteOne(["id" => $idStr]);

    if ($result->getDeletedCount() === 0 && strlen($idStr) === 24 && ctype_xdigit($idStr) && class_exists("MongoDB\\BSON\\ObjectId")) {
        $result = $requestsCollection->deleteOne(["_id" => new \MongoDB\BSON\ObjectId($idStr)]);
    }

    if ($result->getDeletedCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Request not found"]);
        exit;
    }

    echo json_encode(["success" => true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Delete failed"]);
}
