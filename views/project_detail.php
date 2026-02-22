<?php

require_once __DIR__ . '/../models/TaskDetail.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../models/Task.php';

$project_id = $_GET['id'];

$taskModel = new Task();
$taskModel->project_id = $project_id;

/* ===============================
   TAMBAH TASK
=============================== */
if (isset($_POST['add_task'])) {
    $taskModel->title = $_POST['title'];
    $taskModel->description = $_POST['description'];
    $taskModel->deadline = $_POST['deadline'];
    $taskModel->create();

    header("Location: project_detail.php?id=" . $project_id);
    exit();
}

/* ===============================
   UPDATE STATUS
=============================== */
if (isset($_GET['status']) && isset($_GET['task_id'])) {
    $taskModel->id = $_GET['task_id'];
    $taskModel->status = $_GET['status'];
    $taskModel->updateStatus();

    header("Location: project_detail.php?id=" . $project_id);
    exit();
}

/* ===============================
   HAPUS TASK
=============================== */
if (isset($_GET['delete'])) {
    $taskModel->id = $_GET['delete'];
    $taskModel->delete();

    header("Location: project_detail.php?id=" . $project_id);
    exit();
}

/* ===============================
   TAMBAH DETAIL CHECKLIST
=============================== */
if (isset($_POST['add_detail'])) {
    $detail = new TaskDetail();
    $detail->task_id = $_POST['task_id'];
    $detail->description = $_POST['detail_description'];
    $detail->create();

    header("Location: project_detail.php?id=" . $project_id);
    exit();
}

/* ===============================
   TOGGLE CHECKBOX (FIXED)
=============================== */
if (isset($_GET['toggle_detail'])) {

    $detail = new TaskDetail();
    $detail->id = $_GET['toggle_detail'];

    // toggle dulu
    $detail->toggleStatus();

    // ambil task_id yang terkait
    $task_id = $detail->getTaskIdByDetail();

    // cek dan update status task
    $detail->checkAndUpdateTaskStatus($task_id);

    header("Location: project_detail.php?id=" . $project_id);
    exit();
}

$tasks = $taskModel->getByProject();
?>

<h2>Task List</h2>

<form method="POST">
    <input type="text" name="title" placeholder="Judul Task" required><br>
    <textarea name="description" placeholder="Deskripsi"></textarea><br>
    <input type="date" name="deadline"><br>
    <button type="submit" name="add_task">Tambah Task</button>
</form>

<hr>

<?php
$statuses = ['todo', 'doing', 'done'];

foreach ($statuses as $status):
    echo "<h3>" . strtoupper($status) . "</h3>";

    foreach ($tasks as $task):
        if ($task['status'] === $status):
?>

<div style="border:1px solid black; padding:10px; margin:10px;">
    <strong><?php echo $task['title']; ?></strong><br>
    <?php echo $task['description']; ?><br>
    Deadline: <?php echo $task['deadline']; ?><br>

    <?php
    $detailModel = new TaskDetail();
    $detailModel->task_id = $task['id'];
    $details = $detailModel->getByTask();
    ?>

    <h4>Checklist:</h4>

    <form method="POST">
        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
        <input type="text" name="detail_description" placeholder="Tambah poin kerja" required>
        <button type="submit" name="add_detail">Tambah</button>
    </form>

    <?php foreach ($details as $detail): ?>
        <div>
            <form method="GET" style="display:inline;">
                <input type="hidden" name="id" value="<?php echo $project_id; ?>">
                <input type="hidden" name="toggle_detail" value="<?php echo $detail['id']; ?>">
                <input type="checkbox"
                    onchange="this.form.submit()"
                    <?php if($detail['is_completed']) echo "checked"; ?>>
            </form>
            <?php echo $detail['description']; ?>
        </div>
    <?php endforeach; ?>

    <?php
    $total = count($details);
    $completed = 0;

    foreach ($details as $d) {
        if ($d['is_completed']) $completed++;
    }

    $progress = $total > 0 ? round(($completed/$total)*100) : 0;
    ?>

    <p><strong>Progress: <?php echo $progress; ?>%</strong></p>

    <a href="?id=<?php echo $project_id; ?>&task_id=<?php echo $task['id']; ?>&status=todo">ToDo</a>
    <a href="?id=<?php echo $project_id; ?>&task_id=<?php echo $task['id']; ?>&status=doing">Doing</a>
    <a href="?id=<?php echo $project_id; ?>&task_id=<?php echo $task['id']; ?>&status=done">Done</a>
    <a href="?id=<?php echo $project_id; ?>&delete=<?php echo $task['id']; ?>">Hapus</a>

</div>

<?php
        endif;
    endforeach;
endforeach;
?>

<a href="dashboard.php">Kembali</a>