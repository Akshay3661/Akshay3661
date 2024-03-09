<?php
class Database {
    private $host = "localhost";
    private $username = "root"; 
    private $password = ""; 
    private $db_name = "blogsDB"; 
    public $conn;

    public function getConnection() {
        if (!$this->conn) {
            $this->conn = new mysqli($this->host, $this->username, $this->password);

            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }

            $this->createDatabase();
            $this->conn->select_db($this->db_name);
            $this->createUsersTable();
            $this->createPostsTable();
        }

        return $this->conn;
    }

    private function createDatabase() {
        $createDatabaseQuery = "CREATE DATABASE IF NOT EXISTS " . $this->db_name;
        if ($this->conn->query($createDatabaseQuery) === false) {
            die("Database creation failed: " . $this->conn->error);
        }
    }

    private function createUsersTable() {
        $query = "CREATE TABLE IF NOT EXISTS usersDB (
            user_id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL
        )";

        if ($this->conn->query($query) === false) {
            die("Table creation failed: " . $this->conn->error);
        }
    }

    private function createPostsTable() {
        $query = "CREATE TABLE IF NOT EXISTS posts (
            post_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES usersDB(user_id)
        )";

        if ($this->conn->query($query) === false) {
            die("Table creation failed: " . $this->conn->error);
        }
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}


// define("UPLOAD_SRC", $_SERVER['DOCUMENT_ROOT'] . "/My website/PHPCRUD/Database/uploads/");
// define("FETCH_SRC", "http://127.0.0.1/My website/PHPCRUD/Database/uploads/");