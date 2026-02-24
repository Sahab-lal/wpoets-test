<?php

function db_connect(): mysqli
{
    $db = [
        'host' => '127.0.0.1',
        'port' => 8889,
        'name' => 'wpoets_test',
        'user' => 'root',
        'pass' => 'root',
        'charset' => 'utf8mb4',
    ];

    $mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name'], $db['port']);

    if ($mysqli->connect_errno) {
        throw new RuntimeException('Connection failed: ' . $mysqli->connect_error);
    }

    $mysqli->set_charset($db['charset']);
    return $mysqli;
}

function db_fetch_all(mysqli $mysqli, string $sql): array
{
    $result = $mysqli->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
