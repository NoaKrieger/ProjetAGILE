<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/RoomInfo.php';

try {
    $roomService = new RoomInfo($pdo);
    $minCapacity = isset($_GET['capacity_min']) ? (int)$_GET['capacity_min'] : 0;
    $equipments = [];
    if (!empty($_GET['equipments'])) {
        $equipments = is_array($_GET['equipments']) 
            ? $_GET['equipments'] 
            : explode(',', $_GET['equipments']);
    }

    $rooms = $roomService->getRooms($minCapacity, $equipments);

    echo json_encode([
        'success' => true,
        'count' => count($rooms),
        'data' => $rooms
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}