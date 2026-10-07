<?php
class RoomInfo {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getRooms(int $minCapacity = 0, array $requiredEquipments = []): array {
        $sql = "
            SELECT 
                r.id,
                r.name,
                r.building,
                r.floor,
                r.capacity,
                GROUP_CONCAT(e.name SEPARATOR ',') AS equipments
            FROM rooms r
            LEFT JOIN room_equipments re ON r.id = re.room_id
            LEFT JOIN equipments e ON re.equipment_id = e.id
            WHERE r.is_active = 1
              AND r.capacity >= :min_capacity
            GROUP BY r.id
            ORDER BY r.building ASC, r.name ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':min_capacity' => $minCapacity]);
        $rows = $stmt->fetchAll();
        $rooms = [];
        foreach ($rows as $row) {
            $equipmentsList = $row['equipments'] ? explode(',', $row['equipments']) : [];

            if (!empty($requiredEquipments)) {
                $hasAllEquipments = count(array_intersect($requiredEquipments, $equipmentsList)) === count($requiredEquipments);
                if (!$hasAllEquipments) {
                    continue;
                }
            }

            $rooms[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'building' => $row['building'],
                'floor' => (int)$row['floor'],
                'capacity' => (int)$row['capacity'],
                'equipments' => $equipmentsList
            ];
        }

        return $rooms;
    }
}