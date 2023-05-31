<?php

class User {
    private $connection;
    private $userTable = 'users';

    public function __construct($connection) {
        $this->connection = $connection;
    }

    public function createUser($name, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO $this->userTable (name, email, password) VALUES (?, ?, ?)";
        $statement = $this->connection->prepare($sql);
        $statement->bind_param("sss", $name, $email, $hashedPassword);
        $statement->execute();
        $statement->close();
    }

    public function getUser($userId) {
        $sql = "SELECT id, name, email FROM $this->userTable WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->bind_param("i", $userId);
        $statement->execute();
        $result = $statement->get_result();
        $user = $result->fetch_assoc();
        $statement->close();
        return $user;
    }

    public function updateUser($userId, $name, $email) {
        $sql = "UPDATE $this->userTable SET name = ?, email = ? WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->bind_param("ssi", $name, $email, $userId);
        $statement->execute();
        $statement->close();
    }

    public function deleteUser($userId) {
        $sql = "DELETE FROM $this->userTable WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->bind_param("i", $userId);
        $statement->execute();
        $statement->close();
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM $this->userTable WHERE email = ?";
        $statement = $this->connection->prepare($sql);
        $statement->bind_param("s", $email);
        $statement->execute();
        $result = $statement->get_result();
        $user = $result->fetch_assoc();
        $statement->close();
        return $user;
    }

    public function getUsers() {
        $sql = "SELECT id, name, email FROM $this->userTable";
        $result = $this->connection->query($sql);

        if ($result->num_rows > 0) {
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            return $users;
        } else {
            return null;
        }
    }
}

