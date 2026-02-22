<?php
require_once __DIR__ . '/../config/Database.php';

class TaskDetail {

    private $conn;
    private $table = "task_details";

    public $id;
    public $task_id;
    public $description;
    public $is_completed;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  (task_id, description)
                  VALUES (:task_id, :description)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':task_id', $this->task_id);
        $stmt->bindParam(':description', $this->description);

        return $stmt->execute();
    }
    public function delete() {
    $query = "DELETE FROM task_details WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $this->id);
    return $stmt->execute();
}
    public function getByTask() {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE task_id = :task_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':task_id', $this->task_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toggleStatus() {

        $query = "SELECT is_completed FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $newStatus = $row['is_completed'] ? 0 : 1;

        $update = "UPDATE " . $this->table . "
                   SET is_completed = :status
                   WHERE id = :id";

        $stmt = $this->conn->prepare($update);
        $stmt->bindParam(':status', $newStatus);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function getTaskIdByDetail() {

        $query = "SELECT task_id FROM " . $this->table . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['task_id'];
    }

    public function checkAndUpdateTaskStatus($task_id) {

        // hitung total
        $query = "SELECT COUNT(*) as total,
                  SUM(is_completed) as done
                  FROM " . $this->table . "
                  WHERE task_id = :task_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $total = $row['total'];
        $done = $row['done'];

        if ($total == 0) {
    $newStatus = "todo";
} elseif ($done == 0) {
    $newStatus = "todo";
} elseif ($done < $total) {
    $newStatus = "doing";
} else {
    $newStatus = "done";
}

        $update = "UPDATE tasks SET status = :status WHERE id = :task_id";
        $stmt = $this->conn->prepare($update);
        $stmt->bindParam(':status', $newStatus);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();
    }
}