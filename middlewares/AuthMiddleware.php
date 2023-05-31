<?php

class AuthMiddleware {
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    public function authenticate() {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $token = $headers['Authorization'];
            if ($this->verifyToken($token)) {
                return true;
            }
        }
        http_response_code(401);
        exit('Unauthorized');
    }

    private function verifyToken($token) {
        $sql = "SELECT * FROM tokens WHERE token = ?";
        $statement = $this->connection->prepare($sql);
        $statement->bind_param("s", $token);
        $statement->execute();
        $result = $statement->get_result();
        $statement->close();
        return $result->num_rows > 0;
    }

    public function generateToken() {
        $token = bin2hex(random_bytes(32));
        $sql = "INSERT INTO tokens (token) VALUES (?)";
        $statement = $this->connection->prepare($sql);
        $statement->bind_param("s", $token);
        $statement->execute();
        $statement->close();
        return $token;
    }
}
