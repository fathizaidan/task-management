<?php
require_once __DIR__ . '/../config/Database.php';

class TaskAttachment {

    private $conn;
    private $table = "task_attachments";

    public $id;
    public $task_id;
    public $file_name;
    public $file_path;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function create() {

        $query = "INSERT INTO {$this->table}
                  (task_id, file_name, file_path)
                  VALUES (:task_id, :file_name, :file_path)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":task_id", $this->task_id);
        $stmt->bindParam(":file_name", $this->file_name);
        $stmt->bindParam(":file_path", $this->file_path);

        return $stmt->execute();
    }

    public function getByTask() {

        $query = "SELECT * FROM {$this->table}
                  WHERE task_id = :task_id
                  ORDER BY uploaded_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":task_id", $this->task_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete() {

        // ambil file dulu untuk dihapus dari folder
        $query = "SELECT file_path FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($file) {
            $filePath = __DIR__ . "/../../upload/" . $file['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
}