<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit();
}

require_once '../controllers/ProjectController.php';

$controller = new ProjectController();

// Tambah project
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->create($_POST);
    header("Location: index.php?page=dashboard");
    exit();
}

// Hapus project
if (isset($_GET['delete'])) {
    $controller->delete($_GET['delete']);
    header("Location: index.php?page=dashboard");
    exit();
}

$projects = $controller->getAll();
?>

<div class="page-wrapper">

    <div class="dashboard-top">
        <div>
            <h2>Welcome, <?php echo $_SESSION['user_name']; ?></h2>
            <p class="subtitle">Manage your projects efficiently</p>
        </div>
        <a href="index.php?page=logout" class="logout-btn">Logout</a>
    </div>

    <div class="create-project-card">
        <h3>Create New Project</h3>
        <form method="POST" class="create-project-form">
            <input type="text" name="title" placeholder="Enter project name..." required>
            <button type="submit">Create Project</button>
        </form>
    </div>

    <h3 class="project-section-title">Your Projects</h3>

    <div class="project-grid">
        <?php foreach ($projects as $project): ?>
            <div class="project-card">
                <div class="project-card-body">
                    <h4><?php echo htmlspecialchars($project['title']); ?></h4>
                </div>

                <div class="project-card-footer">
                    <a href="index.php?page=project-detail&id=<?php echo $project['id']; ?>">
                        Open
                    </a>
                    <a class="delete-link"
                       href="index.php?page=dashboard&delete=<?php echo $project['id']; ?>"
                       onclick="return confirm('Delete this project?')">
                        Delete
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>