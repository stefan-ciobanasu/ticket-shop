<?php

namespace Model;

class Repository
{
    public function getAllEvents(): array
    {
        $result = array();
        $db = Database::getInstance();
        $statement = $db->prepare("SELECT * FROM `tickets`.`events` ORDER BY `id` LIMIT 100");
        $statement->execute();
        $results = $statement->get_result();
        if ($results->num_rows > 0) {
            while ($row = $results->fetch_assoc()) {
                $result[] = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'event_date' => $row['event_date'],
                    'logo' => $row['logo'],
                ];
            }
            $statement->close();
        }
        return $result;
    }
}