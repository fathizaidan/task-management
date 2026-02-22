<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../controllers/ProjectController.php';

$controller = new ProjectController();

// Tambah project
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->create($_POST);
}

// Hapus project
if (isset($_GET['delete'])) {
    $controller->delete($_GET['delete']);
}

// Ambil semua project
$projects = $controller->getAll();
?>

<h2>Welcome, <?php echo $_SESSION['user_name']; ?></h2>
<a href="login.php">Logout</a>

<h3>Tambah Project</h3>
<form method="POST">
    <input type="text" name="title" placeholder="Nama Project" required>
    <button type="submit">Tambah</button>
</form>

<h3>Daftar Project</h3>

<?php foreach ($projects as $project): ?>
    <div style="border:1px solid black; padding:10px; margin:10px 0;">

        <a href="project_detail.php?id=<?php echo $project['id']; ?>">
            <?php echo $project['title']; ?>
        </a>

        |
        <a href="?delete=<?php echo $project['id']; ?>">Hapus</a>

    </div>
<?php endforeach; ?>