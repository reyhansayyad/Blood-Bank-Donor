<?php
require __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client;

try {
    $client = new Client("mongodb://localhost:27017");
    $db = $client->bloodbank_db;

    $donorsCollection = $db->donors;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "MongoDB connection failed"]);
    exit;
}
