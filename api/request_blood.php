<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../config/mongodb.php";

$raw = file_get_contents("php://input");
$data = $raw ? json_decode($raw, true) : null;

if (!$data || !isset($data["userId"]) || !isset($data["bloodGroup"]) || !isset($data["quantity"])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit;
}

try {
    $requestsCollection = $db->blood_requests;
    $requestId = bin2hex(random_bytes(12));
    $now = date("c");
    $requestsCollection->insertOne([
        "id"          => $requestId,
        "userId"      => $data["userId"],
        "bloodGroup"  => $data["bloodGroup"],
        "quantity"    => (int) $data["quantity"],
        "status"      => "pending",
        "requestedAt" => $now,
        "updatedAt"   => $now
    ]);
    echo json_encode(["success" => true, "message" => "Blood request submitted"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Request failed. Please try again."]);
}
