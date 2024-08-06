<?php

namespace Model;

class User
{
    private ?int $userId = null;
    private ?string $username = null;
    private ?string $name = null;
    private ?int $level = null;
    private ?array $tickets = null;

    public function __construct()
    {
        if (!isset($_SESSION)) {
            throw new \Exception('Ceva a mers prost!');
        }
        if (isset($_SESSION['user_id']) && isset($_COOKIE['token'])) {
            $this->verifyUserToken($_SESSION['user_id'], $_COOKIE['token']);
        }
    }

    public function isLoggedIn(): bool
    {
        if (is_int($this->userId)) {
            return true;
        }
        return false;
    }

    public function verifyUserToken(string $userId, string $token): void
    {
        $db = Database::getInstance();
        $userId = (int)$userId;
        $token = $db->real_escape_string($token);
        /** @var \mysqli_stmt $statement */
        $statement = $db->prepare("SELECT `id`, `username`, `name`, `user_level` FROM `tickets`.`users` WHERE `id` = ? AND `auth_token` = ? LIMIT 1");
        $statement->bind_param("is", $userId, $token);
        $statement->execute();
        $statement->bind_result($this->userId, $this->username, $this->name, $this->level);
        $statement->fetch();
        $statement->close();
    }

    public function loginUser(string $username, string $password): void
    {
        $db = Database::getInstance();
        $username = $db->real_escape_string($username);
        $password = md5($db->real_escape_string($password));
        /** @var \mysqli_stmt $statement */
        $statement = $db->prepare("SELECT `id`, `username`, `name`, `user_level` FROM `tickets`.`users` WHERE `username` = ? AND `userpass` = ? LIMIT 1");
        $statement->bind_param("ss", $username, $password);
        $statement->execute();
        $results = $statement->get_result();
        if ($results->num_rows > 0) {
            $result = $results->fetch_assoc();
            $statement->close();
            $this->userId = $result['id'];
            $this->username = $result['username'];
            $this->name = $result['name'];
            $this->level = $result['user_level'];
            $statement = $db->prepare("UPDATE `tickets`.`users` SET `auth_token` = ? WHERE `id` = ?");
            $token = hash('md5',random_bytes(64));
            $statement->bind_param("si", $token, $this->userId);
            $statement->execute();
            $statement->close();
            $_SESSION['user_id'] = $this->userId;
            setcookie('token',$token,time() + 86400,'/','localhost');
            $_SESSION['name'] = $this->name;
            $_SESSION['level'] = $this->level;
        }
    }

    public function createUser(string $username, string $password, string $name, string $email): bool
    {
        $db = Database::getInstance();
        $username = $db->real_escape_string($username);
        $password = md5($db->real_escape_string($password));
        $name = $db->real_escape_string($name);
        $email = $db->real_escape_string($email);
        $token = hash('md5',random_bytes(64));
        $level = 2;
        $statement = $db->prepare("INSERT INTO `tickets`.`users` VALUES (NULL, ?, ?, ?, ?, ?, ?)");
        $statement->bind_param("sssssi", $username, $password, $email, $name, $token, $level);
        $statement->execute();
        if ($statement->affected_rows == 1) {
            $statement->close();
            return true;
        }
        return false;
    }

    public function forgotPassword(string $email): ?string
    {
        $db = Database::getInstance();
        $email = $db->real_escape_string($email);
        $statement = $db->prepare("SELECT u.`id`, rp.`expire` FROM `tickets`.`users` u
            LEFT JOIN `tickets`.`reset_password` rp ON rp.user_id = u.id
            WHERE u.`email` = ? LIMIT 1");
        $statement->bind_param("s", $email);
        $statement->execute();
        $results = $statement->get_result();
        if ($results->num_rows > 0) {
            $row = $results->fetch_assoc();
            $userId = $row['id'];
            $expireDate = $row['expire'];
            if (strtotime($expireDate) > time()) {
                return null;
            }
            $statement->close();
            $resetToken = hash('md5',random_bytes(64));
            $expireDate = (new \DateTime("+1 hour"))->format('Y-m-d H:i:s');
            $statement = $db->prepare("INSERT INTO `tickets`.`reset_password` VALUES (NULL, ?, ?, ?)");
            $statement->bind_param("iss", $userId, $resetToken, $expireDate);
            $statement->execute();
            if ($statement->affected_rows == 1) {
                $statement->close();
                mail($email,'Resetare parola utilizator', "Accesati linkul http://localhost/user/reset_password?email=$email&token=$resetToken");
                return "/user/reset_password?email=$email&token=$resetToken";
            }
            return null;
        }
        return null;
    }

    public function canResetPassword(string $email, string $token): bool
    {
        $db = Database::getInstance();
        $email = $db->real_escape_string($email);
        $token = $db->real_escape_string($token);
        $date = (new \DateTime())->format('Y-m-d H:i:s');
        $statement = $db->prepare("SELECT rp.`id` FROM `tickets`.`reset_password` rp 
            INNER JOIN `tickets`.`users` u on rp.`user_id` = u.`id`
            WHERE u.`email` = ? AND rp.`reset_token` = ? AND rp.`expire` > ? LIMIT 1");
        $statement->bind_param("sss", $email, $token, $date);
        $statement->execute();
        $results = $statement->get_result();
        if ($results->num_rows > 0) {
            return $results->fetch_assoc()['id'];
        }
        return false;
    }

    public function resetPassword(string $email, string $password): bool
    {
        $db = Database::getInstance();
        $email = $db->real_escape_string($email);
        $password = md5($db->real_escape_string($password));
        $statement = $db->prepare("UPDATE `tickets`.`users` u SET u.`userpass` = ? where u.`email` = ?");
        $statement->bind_param("ss", $password, $email);
        $statement->execute();
        if ($statement->affected_rows == 1) {
            $statement->close();
            $statement = $db->prepare("DELETE FROM `tickets`.`reset_password` 
                WHERE `user_id` IN (SELECT `id` FROM `tickets`.`users` WHERE `email` = ?)");
            $statement->bind_param("s", $email);
            $statement->execute();
            if ($statement->affected_rows == 1) {
                $statement->close();
                return true;
            }
        }
        return false;
    }

    public function getTickets(): array
    {
        if ($this->tickets === null) {
            $this->fetchTickets();
            if ($this->tickets === null) {
                return [];
            }
        }
        return $this->tickets;
    }

    private function fetchTickets(): void
    {
        $db = Database::getInstance();
        $userId = $this->getUserId();
        $statement = $db->prepare("SELECT ut.`ticket_count`, e.`id`, e.`title`, e.`event_date`, e.`logo` FROM `tickets`.`user_tickets` ut
            INNER JOIN `tickets`.`events` e on ut.`event_id` = e.`id`
            WHERE ut.`user_id` = ?");
        $statement->bind_param("i", $userId);
        $statement->execute();
        $results = $statement->get_result();
        if ($results->num_rows > 0) {
            while ($row = $results->fetch_assoc()) {
                $event = (new Event())
                    ->setId($row['id'])
                    ->setTitle($row['title'])
                    ->setEventDate(new \DateTime($row['event_date']))
                    ->setLogo($row['logo']);
                $this->tickets[] = new Ticket($this->getUserId(), $event, $row['ticket_count']);
            }
            $statement->close();
        }
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }
}