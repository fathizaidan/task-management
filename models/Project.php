<?php
require_once __DIR__ . '/../config/Database.php';

class Project {

    private $conn;
    private $table = "projects";

    public $id;
    public $user_id;
    public $title;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  (user_id, title)
                  VALUES (:user_id, :title)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':title', $this->title);

        return $stmt->execute();
    }

    public function getByUser() {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table . "
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
}