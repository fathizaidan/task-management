<?php
require_once __DIR__ . '/../models/Project.php';

class ProjectController {

    public function create($data) {
        $project = new Project();

        $project->user_id = $_SESSION['user_id'];
        $project->title = htmlspecialchars(strip_tags($data['title']));

        return $project->create();
    }

    public function getAll() {
        $project = new Project();
        $project->user_id = $_SESSION['user_id'];

        return $project->getByUser();
    }

    public function delete($id) {
        $project = new Project();
        $project->id = $id;

        return $project->delete();
    }
}