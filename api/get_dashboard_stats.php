<?php
require_once "../config/mongodb.php";

$pipeline = [
    ['$match' => ['available' => true]],
    ['$group' => ['_id' => '$blood_group', 'count' => ['$sum' => 1]]]
];

$cursor = $donorsCollection->aggregate($pipeline);

$data = [];
foreach ($cursor as $d) {
    $data[] = [
        "blood_group" => $d["_id"],
        "count" => $d["count"]
    ];
}

echo json_encode($data);
