<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/mongodb.php";

try {
    $requestsCollection = $db->blood_requests;
    $cursor = $requestsCollection->find(
        [],
        ["sort" => ["requestedAt" => -1]]
    );

    $list = [];
    foreach ($cursor as $doc) {
        $id = isset($doc["id"]) ? $doc["id"] : (string) $doc["_id"];
        $list[] = [
            "_id"        => $id,
            "userId"     => $doc["userId"] ?? "",
            "bloodGroup" => $doc["bloodGroup"] ?? "",
            "quantity"   => $doc["quantity"] ?? 0,
            "status"     => $doc["status"] ?? "pending",
        ];
    }
    echo json_encode($list);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to load requests"]);
}
