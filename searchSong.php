<?php

$method = $_SERVER['REQUEST_METHOD'];


$data = [
    'recieved' => [
        'method' => $method,
        'post' => $_POST,
        'get' => $_GET,
        'request' => $_REQUEST,
        'server' => $_SERVER,
        'all' => getallheaders(),
    ],
];

header('Content-Type:application/json');

echo json_encode($data);