<?php
header("Content-Type: application/json");
error_reporting(0);
ini_set("display_errors", 0);

try {
    require_once __DIR__ . "/../../config/mongodb.php";
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Config error"]);
    exit;
}

if (!isset($db)) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database not available"]);
    exit;
}

$raw = file_get_contents("php://input");
$data = $raw ? json_decode($raw, true) : null;

if (!$data || !isset($data["id"]) || $data["id"] === "" || !isset($data["status"]) || $data["status"] === "") {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit;
}

$status = trim($data["status"]);
$allowed = ["accepted", "rejected", "pending"];
if (!in_array($status, $allowed, true)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid status"]);
    exit;
}

$idStr = trim((string) $data["id"]);
if ($idStr === "") {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid request id"]);
    exit;
}

try {
    $requestsCollection = $db->blood_requests;
    $update = [
        [ '$set' => [ 'status' => $status, 'updatedAt' => date('c') ] ]
    ];
    $result = $requestsCollection->updateOne(["id" => $idStr], $update);

    if ($result->getMatchedCount() === 0 && strlen($idStr) === 24 && ctype_xdigit($idStr) && class_exists("MongoDB\\BSON\\ObjectId")) {
        $result = $requestsCollection->updateOne(
            ["_id" => new \MongoDB\BSON\ObjectId($idStr)],
            $update
        );
    }

    if ($result->getMatchedCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Request not found"]);
        exit;
    }

    echo json_encode(["success" => true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Update failed: " . $e->getMessage()]);
}
