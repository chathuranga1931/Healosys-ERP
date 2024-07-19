<?php

class Database {
    private $connection;
    private $host;
    private $username;
    private $password;
    private $database;

    public function __construct() {
        $config = include('config.php');

        $this->host = $config['host'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->database = $config['database'];

        $this->connect();
    }

    private function connect() {
        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);

        if ($this->connection->connect_error) {
            die('Connection failed: ' . $this->connection->connect_error);
        }
    }

    public function query($sql) {
        $result = $this->connection->query($sql);

        if ($result === FALSE) {
            die('Error: ' . $this->connection->error);
        }

        return $result;
    }

    public function fetchAll($sql) {
        $result = $this->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function fetchOne($sql) {
        $result = $this->query($sql);
        return $result->fetch_assoc();
    }

    public function execute($sql) {
        $this->query($sql);
        return $this->connection->affected_rows;
    }

    public function getConnection() {
        return $this->connection;
    }


    public function close() {
        $this->connection->close();
    }

    // public function __destruct() {
    //     $this->connection->close();
    // }
}

?>
