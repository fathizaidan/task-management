<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit();
}

require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/TaskDetail.php';

$project_id = $_GET['id'] ?? null;

if (!$project_id) {
    echo "Project tidak ditemukan";
    exit();
}

$taskModel = new Task();
$taskModel->project_id = $project_id;

/* ================= HANDLE ACTION ================= */

// TAMBAH TASK
if (isset($_POST['add_task'])) {
    $taskModel->title = $_POST['title'];
    $taskModel->description = $_POST['description'];
    $taskModel->deadline = $_POST['deadline'];
    $taskModel->create();

    header("Location: index.php?page=project-detail&id=$project_id");
    exit();
}

// TAMBAH DETAIL
if (isset($_POST['add_detail'])) {
    $detail = new TaskDetail();
    $detail->task_id = $_POST['task_id'];
    $detail->description = $_POST['detail_description'];
    $detail->create();
    $detail->checkAndUpdateTaskStatus($_POST['task_id']);

    header("Location: index.php?page=project-detail&id=$project_id");
    exit();
}

// TOGGLE CHECKBOX
if (isset($_GET['toggle_detail'])) {
    $detail = new TaskDetail();
    $detail->id = $_GET['toggle_detail'];
    $detail->toggleStatus();
    $task_id = $detail->getTaskIdByDetail();
    $detail->checkAndUpdateTaskStatus($task_id);

    header("Location: index.php?page=project-detail&id=$project_id");
    exit();
}
// DELETE DETAIL
if (isset($_GET['delete_detail'])) {
    $detail = new TaskDetail();
    $detail->id = $_GET['delete_detail'];
    $detail->delete();

    header("Location: index.php?page=project-detail&id=$project_id");
    exit();
}

// UPDATE STATUS
if (isset($_GET['status']) && isset($_GET['task_id'])) {
    $taskModel->id = $_GET['task_id'];
    $taskModel->status = $_GET['status'];
    $taskModel->updateStatus();

    header("Location: index.php?page=project-detail&id=$project_id");
    exit();
}

// HAPUS TASK
if (isset($_GET['delete'])) {
    $taskModel->id = $_GET['delete'];
    $taskModel->delete();

    header("Location: index.php?page=project-detail&id=$project_id");
    exit();
}

$tasks = $taskModel->getByProject();
?>
<div class="page-wrapper">
<h2 class="board-title">Project Board</h2>

<!-- FORM TAMBAH TASK -->
<div class="add-task-wrapper">
    <form method="POST" class="add-task-form">
        <input type="text" name="title" placeholder="Task title..." required>
        <textarea name="description" placeholder="Description"></textarea>
        <input type="date" name="deadline">
        <button type="submit" name="add_task">Add Task</button>
    </form>
</div>

<!-- BOARD -->
<div class="board-wrapper">

<?php
$statuses = [
    'todo' => 'To Do',
    'doing' => 'In Progress',
    'done' => 'Done'
];

foreach ($statuses as $key => $label):
?>

<div class="board-column">
    <div class="column-header">
        <h3><?php echo $label; ?></h3>
    </div>

    <div class="column-body">

<?php
foreach ($tasks as $task):

if ($task['status'] === $key):

    $detailModel = new TaskDetail();
    $detailModel->task_id = $task['id'];
    $details = $detailModel->getByTask();

    $total = count($details);
    $completed = 0;
    foreach ($details as $d) {
        if ($d['is_completed']) $completed++;
    }

    $progress = $total > 0 ? round(($completed / $total) * 100) : 0;
?>

<!-- CARD -->
<div class="board-card" onclick="openModal(<?php echo $task['id']; ?>)">
    <div class="card-title">
        <?php echo htmlspecialchars($task['title']); ?>
    </div>

    <?php if (!empty($task['description'])): ?>
        <div class="card-desc">
            <?php echo htmlspecialchars($task['description']); ?>
        </div>
    <?php endif; ?>

    <div class="card-deadline">
        📅 <?php echo $task['deadline']; ?>
    </div>

    <?php if ($total > 0): ?>
        <div class="progress-wrapper">
            <div class="progress-bar" style="width: <?php echo $progress; ?>%"></div>
        </div>
        <div class="progress-text"><?php echo $progress; ?>%</div>
    <?php endif; ?>
</div>

<!-- MODAL -->
<div id="modal-<?php echo $task['id']; ?>" class="task-modal">

    <div class="modal-content">

        <div class="modal-header">
            <h3><?php echo htmlspecialchars($task['title']); ?></h3>
            <span class="close-btn" onclick="closeModal(<?php echo $task['id']; ?>)">×</span>
        </div>

        <p class="modal-desc">
            <?php echo htmlspecialchars($task['description']); ?>
        </p>

        <div class="modal-deadline">
            📅 Deadline: <?php echo $task['deadline']; ?>
        </div>

        <div class="modal-progress">
            <div class="progress-wrapper">
                <div class="progress-bar" style="width: <?php echo $progress; ?>%"></div>
            </div>
            <small><?php echo $progress; ?>% complete</small>
        </div>
        <a href="index.php?page=project-detail&id=<?= $project_id ?>&delete=<?= $task['id'] ?>"
   class="delete-task-btn"
   onclick="return confirm('Yakin ingin menghapus task ini?')">
   🗑 Delete Task
</a>
        <h4>Add Task Details</h4>

        <form method="POST" class="detail-form">
            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
            <input type="text" name="detail_description" placeholder="Add detail item..." required>
            <button type="submit" name="add_detail">Add</button>
        </form>

        <?php foreach ($details as $detail): ?>
    <div class="checklist-item">

        <!-- Toggle -->
        <form method="GET" style="margin:0;">
            <input type="hidden" name="page" value="project-detail">
            <input type="hidden" name="id" value="<?= $project_id ?>">
            <input type="hidden" name="toggle_detail" value="<?= $detail['id'] ?>">
            <input type="checkbox"
                   onchange="this.form.submit()"
                   <?= $detail['is_completed'] ? 'checked' : '' ?>>
        </form>

        <!-- Text -->
        <span class="check-text">
            <?= htmlspecialchars($detail['description']) ?>
        </span>

        <!-- Delete Button -->
        <a href="index.php?page=project-detail&id=<?= $project_id ?>&delete_detail=<?= $detail['id'] ?>"
           class="delete-detail-btn"
           onclick="return confirm('Hapus checklist ini?')">
           ✕
        </a>

    </div>
<?php endforeach; ?>

    </div>
</div>

<!-- DETAIL PANEL -->
<div id="detail-<?php echo $task['id']; ?>" class="task-detail-panel">

    <h4>Checklist</h4>

    <!-- FORM TAMBAH DETAIL -->
    <form method="POST" class="detail-form">
        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
        <input type="text" name="detail_description" placeholder="Add checklist item..." required>
        <button type="submit" name="add_detail">Add</button>
    </form>

    <!-- CHECKLIST ITEMS -->
<?php foreach ($details as $detail): ?>
    <div class="checklist-item">

        <!-- Toggle Checkbox -->
        <form method="GET">
            <input type="hidden" name="page" value="project-detail">
            <input type="hidden" name="id" value="<?= $project_id ?>">
            <input type="hidden" name="toggle_detail" value="<?= $detail['id'] ?>">
            <input type="checkbox"
                   onchange="this.form.submit()"
                   <?= $detail['is_completed'] ? 'checked' : '' ?>>
        </form>

        <span class="check-text">
            <?= htmlspecialchars($detail['description']) ?>
        </span>

        <!-- DELETE DETAIL -->
        <a href="index.php?page=project-detail&id=<?= $project_id ?>&delete_detail=<?= $detail['id'] ?>"
           class="delete-detail-btn"
           onclick="return confirm('Hapus checklist ini?')">
           ✕
        </a>

    </div>
<?php endforeach; ?>

</div>

<?php
endif;
endforeach;
?>

    </div>
</div>

<?php endforeach; ?>

</div>

<br>
<a href="index.php?page=dashboard" class="back-link">← Back to Dashboard</a>

<!-- SCRIPT -->
<script>
function toggleDetail(id) {
    const panel = document.getElementById("detail-" + id);

    if (panel.style.display === "block") {
        panel.style.display = "none";
    } else {
        panel.style.display = "block";
    }
}
function openModal(id) {
    document.getElementById("modal-" + id).style.display = "flex";
}

function closeModal(id) {
    document.getElementById("modal-" + id).style.display = "none";
}

// close modal if click outside
window.onclick = function(e) {
    document.querySelectorAll('.task-modal').forEach(modal => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });
}
</script>