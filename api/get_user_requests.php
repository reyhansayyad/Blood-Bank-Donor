<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../config/mongodb.php";

$uid = isset($_GET["uid"]) ? trim($_GET["uid"]) : "";
if ($uid === "") {
    http_response_code(400);
    echo json_encode([]);
    exit;
}

try {
    $requestsCollection = $db->blood_requests;
    $cursor = $requestsCollection->find(
        ["userId" => $uid],
        ["sort" => ["requestedAt" => -1], "projection" => ["_id" => 0, "userId" => 0, "requestedAt" => 0, "updatedAt" => 0]]
    );
    $list = [];
    foreach ($cursor as $doc) {
        $list[] = [
            "bloodGroup" => $doc["bloodGroup"] ?? "",
            "quantity"   => $doc["quantity"] ?? 0,
            "status"     => $doc["status"] ?? ""
        ];
    }
    echo json_encode($list);
} catch (Exception $e) {
    echo json_encode([]);
}
