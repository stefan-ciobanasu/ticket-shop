<?php

namespace Model;

class Event
{
    private ?int $id = null;
    private string $title;
    private \DateTime $eventDate;
    private string $logo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getEventDate(): \DateTime
    {
        return $this->eventDate;
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setEventDate(\DateTime $eventDate): self
    {
        $this->eventDate = $eventDate;
        return $this;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;
        return $this;
    }

    static public function getById(int $id): ?Event
    {
        $event = null;
        $db = Database::getInstance();
        $statement = $db->prepare("SELECT * FROM `tickets`.`events` WHERE `id`=?");
        $statement->bind_param("i", $id);
        $statement->execute();
        $results = $statement->get_result();
        if ($results->num_rows > 0) {
            $row = $results->fetch_assoc();
            $event = new Event();
            $event->setLogo($row['logo']);
            $event->setId($row['id']);
            $event->setTitle($row['title']);
            $event->setEventDate(new \DateTime($row['event_date']));
        }
        return $event;
    }

    public function save(): bool
    {
        $db = Database::getInstance();
        $title = $this->getTitle();
        $date = $this->getEventDate()->format('Y-m-d H:i:s');
        $logo = $this->getLogo();
        if (!is_null($this->getId())) {
            $statement = $db->prepare("UPDATE `tickets`.`events` SET `title` = ?, `event_date` = ?, `logo` = ? WHERE `id` = ?");
            $statement->bind_param("sssi", $title, $date, $logo, $this->id);
        } else {
            $statement = $db->prepare("INSERT INTO `tickets`.`events` VALUES (null, ?, ?, ?)");
            $statement->bind_param("sss", $title, $date, $logo);
        }
        $statement->execute();
        if ($statement->affected_rows == 1) {
            if ($statement->insert_id !== 0) {
                $this->setId($statement->insert_id);
            }
            $statement->close();
            return true;
        }
        return false;
    }

    public function delete(): bool
    {
        $db = Database::getInstance();
        if (!is_null($this->getId())) {
            $statement = $db->prepare("DELETE FROM `tickets`.`events` WHERE `id`=?");
            $statement->bind_param("i", $this->id);
            $statement->execute();
            if ($statement->affected_rows == 1) {
                $statement->close();
                return true;
            } else {
                $statement->close();
                return false;
            }
        }
        return false;
    }
}
