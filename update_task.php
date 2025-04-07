<?php
session_start();
include 'db.php.inc';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Team Member') {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['task_id']) || empty($_GET['task_id'])) {
    echo "Error: Task ID is missing.";
    exit();
}

$task_id = $_GET['task_id'];

$stmt = $pdo->prepare("
    SELECT 
        t.task_id,
        t.task_name,
        t.description,
        t.priority,
        t.status,
        t.start_date,
        t.end_date,
        t.effort,
        t.project_id,
        t.progress_percentage,
        p.project_title
    FROM 
        tasks t
    JOIN 
        projects p ON t.project_id = p.project_id
    WHERE 
        t.task_id = :task_id
");
$stmt->execute(['task_id' => $task_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    echo "Error: Task not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $progress_percentage = $_POST['progress_percentage'];
    $status = $_POST['status'];

    try {
        if ($progress_percentage < 0 || $progress_percentage > 100) {
            throw new Exception("Progress must be between 0 and 100.");
        }

        if ($status === 'Completed' && $progress_percentage < 100) {
            throw new Exception("Progress must be 100% to mark the task as completed.");
        }

        if ($status === 'In Progress' && $progress_percentage <= 0) {
            throw new Exception("Progress must be greater than 0% for tasks in progress.");
        }

        $stmt = $pdo->prepare("
            UPDATE tasks 
            SET progress_percentage = :progress_percentage, status = :status 
            WHERE task_id = :task_id
        ");
        $stmt->execute([
            'progress_percentage' => $progress_percentage,
            'status' => $status,
            'task_id' => $task_id
        ]);

        $confirmationMessage = "Task updated successfully!";
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Task</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Task Allocator Pro</h1>
    </header>
    <div class="container">
        <main>
            <h2>Update Task</h2>
            <?php if (!empty($confirmationMessage)): ?>
                <div class="success"><?= htmlspecialchars($confirmationMessage) ?></div>
                <a href="assigned_tasks.php">Back to Assigned Tasks</a>
            <?php elseif (!empty($errorMessage)): ?>
                <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
            <?php else: ?>
                <form method="POST">
                    <p><strong>Task ID:</strong> <?= htmlspecialchars($task['task_id']) ?></p>
                    <p><strong>Task Name:</strong> <?= htmlspecialchars($task['task_name']) ?></p>
                    <p><strong>Project Name:</strong> <?= htmlspecialchars($task['project_title']) ?></p>

                    <label for="progress_percentage">Current Progress:</label>
                    <input type="range" id="progress_percentage" name="progress_percentage" min="0" max="100" value="<?= $task['progress_percentage'] ?? 0 ?>">
                    <span id="progress-value"><?= $task['progress_percentage'] ?? 0 ?>%</span>

                    <label for="status">Current Status:</label>
                    <select id="status" name="status" required>
                        <option value="Pending" <?= $task['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="In Progress" <?= $task['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="Completed" <?= $task['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                    </select>

                    <button type="submit">Update Task</button>
                </form>
            <?php endif; ?>
        </main>
    </div>

    <footer>
        <p>&copy; 2025 Task Allocator Pro. All rights reserved.</p>
    </footer>

    <script>
        const progressInput = document.getElementById('progress_percentage');
        const progressValue = document.getElementById('progress-value');

        progressInput.addEventListener('input', function () {
            progressValue.textContent = progressInput.value + '%';
        });
    </script>
</body>
</html>
