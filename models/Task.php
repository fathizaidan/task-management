<?php
require_once __DIR__ . '/../config/Database.php';

class Task {
    private $conn;
    private $table = "tasks";

    public $id;
    public $project_id;
    public $title;
    public $description;
    public $status;
    public $deadline;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (project_id, title, description, deadline) 
                  VALUES (:project_id, :title, :description, :deadline)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':project_id', $this->project_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':deadline', $this->deadline);

        return $stmt->execute();
    }
    
    public function getByProject() {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE project_id = :project_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $this->project_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus() {
        $query = "UPDATE " . $this->table . " 
                  SET status = :status 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

public function delete() {

    // hapus semua detail dulu
    $stmt = $this->conn->prepare("DELETE FROM task_details WHERE task_id = :task_id");
    $stmt->bindParam(':task_id', $this->id);
    $stmt->execute();

    // hapus task
    $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->bindParam(':id', $this->id);

    return $stmt->execute();
}

    public function getWithProject() {
    $query = "SELECT tasks.*, projects.title as project_name
              FROM tasks
              JOIN projects ON tasks.project_id = projects.id
              WHERE projects.user_id = :user_id";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $this->project_id);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}