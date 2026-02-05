<?php
require_once "../config/mongodb.php";

$filter = ['available' => true];

if (!empty($_GET['blood'])) {
    $filter['blood_group'] = $_GET['blood'];
}

$cursor = $donorsCollection->find($filter);

$data = [];
foreach ($cursor as $d) {
    $data[] = [
        "name" => $d["name"],
        "blood_group" => $d["blood_group"],
        "city" => $d["city"],
        "mobile" => $d["mobile"]
    ];
}

echo json_encode($data);
