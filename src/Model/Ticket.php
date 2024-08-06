<?php

namespace Model;

class Ticket
{
    private int $userId;
    private Event $event;
    private int $count;

    public function __construct(int $userId, Event $event, int $count)
    {
        $this->userId = $userId;
        $this->event = $event;
        $this->count = $count;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function save(): bool
    {
        $userId = $this->getUserId();
        $eventId = $this->getEvent()->getId();
        $count = $this->getCount();
        $db = Database::getInstance();
        $statementSearch = $db->prepare('SELECT * FROM `tickets`.`user_tickets` ut WHERE ut.`user_id` = ? AND ut.`event_id` = ?');
        $statementSearch->bind_param("ii", $userId, $eventId);
        $statementSearch->execute();
        $results = $statementSearch->get_result();
        if ($results->num_rows > 0) {
            $row = $results->fetch_assoc();
            $ticketCount = $row['ticket_count'] + $this->getCount();
            $statementUpdate = $db->prepare('UPDATE `tickets`.`user_tickets` SET `ticket_count` = ? WHERE `user_id` = ? AND `event_id` = ?');
            $statementUpdate->bind_param("iii", $ticketCount, $userId, $eventId);
            $statementUpdate->execute();
            if ($statementUpdate->affected_rows == 1) {
                $statementUpdate->close();
                return true;
            }
        } else {
            $statementInsert = $db->prepare('INSERT INTO `tickets`.`user_tickets` VALUES (?, ?, ?)');
            $statementInsert->bind_param("iii", $userId, $eventId, $count);
            $statementInsert->execute();
            if ($statementInsert->affected_rows == 1) {
                $statementInsert->close();
                return true;
            }
        }
        return false;
    }
}