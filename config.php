<?php

const SHOP_ID = 'YOUR_SHOP_ID';
const API_KEY = 'YOUR_API_KEY';
const SUCCESS_URL = 'https://your_success_url';

$db = [
    'host' => 'DB_HOST',
    'name' => 'DB_NAME',
    'user' => 'DB_USER',
    'password' => 'DB_PASS',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ],
];

$dsn = "mysql:host=" . $db['host'] . ";dbname=" . $db['name'] . ";charset=utf8";
$pdo = new PDO($dsn, $db['user'], $db['password'], $db['options']);

$products = [
    1 => [
        'title' => 'Product 1',
        'price' => 1000,
    ],
    2 => [
        'title' => 'Product 2',
        'price' => 2000,
    ],
    3 => [
        'title' => 'Product 3',
        'price' => 3000,
    ],
    4 => [
        'title' => 'Product 4',
        'price' => 4000,
    ],
];
