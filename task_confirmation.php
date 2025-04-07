<?php
session_start();
include 'db.php.inc';
$user_role = $_SESSION['role'] ?? 'Guest'; 

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
        tm.role
    FROM 
        task_team_members tm
    JOIN 
        tasks t ON tm.task_id = t.task_id
    WHERE 
        tm.task_id = :task_id AND tm.user_id = :user_id
");
$stmt->execute([
    'task_id' => $task_id,
    'user_id' => $_SESSION['user_id']
]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    echo "Error: Task not found.";
    exit();
}

$confirmationMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'];

        if ($action === 'accept') {
            $stmt = $pdo->prepare("UPDATE tasks SET status = 'Completed' WHERE task_id = :task_id");
            $stmt->execute(['task_id' => $task_id]);
            $confirmationMessage = "Task successfully accepted.";
        } elseif ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE tasks SET status = 'Pending' WHERE task_id = :task_id");
            $stmt->execute(['task_id' => $task_id]);
            $confirmationMessage = "Task successfully rejected.";
        } else {
            throw new Exception("Invalid action.");
        }
    } catch (Exception $e) {
        $confirmationMessage = "Error: " . $e->getMessage();
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Allocator Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Task Allocator Pro</h1>
        <nav>
        <a href="profile.php">Profile</a> 
        <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
    <nav class="sidebar">
        <?php if ($user_role == 'Manager'): ?>
            <a href="addproject.php?page=add_task" class="<?= $page == 'add_task' ? 'active' : '' ?>">Add Project</a>
            <a href="unassigned_projects.php?page=allocate_team_member" class="<?= $page == 'allocate_team_member' ? 'active' : '' ?>">Allocate Team Member</a>
        <?php endif; ?>

        <?php if ($user_role == 'Project Leader'): ?>
            <a href="create_task.php" class="<?= $page == 'create_task' ? 'active' : '' ?>">Create Task</a>

            <a href="task_list.php" class="<?= $page == 'assign_team_members' ? 'active' : '' ?>">Assign Team Members to Tasks</a>


        <?php endif; ?>

        <?php if ($user_role == 'Team Member'): ?>
            <a href="assigned_tasks.php" class="<?= $page == 'accept_task' ? 'active' : '' ?>">Accept Task Assignments Feature</a>
            <a href="search_tasks.php" class="<?= $page == 'update_task_progress' ? 'active' : '' ?>">Search and Update Task Progress</a>
        <?php endif; ?>

        <a href="task_list_with_details.php">Task Search Functionality Description</a>
    </nav>

</nav>

       

<main>
            <h2>Task Confirmation</h2>
            <?php if (!empty($confirmationMessage)): ?>
                <div class="message">
                    <p><?= htmlspecialchars($confirmationMessage) ?></p>
                </div>
                <a href="assigned_tasks.php">Back to Assigned Tasks</a>
            <?php else: ?>
                <p><strong>Task ID:</strong> <?= htmlspecialchars($task['task_id']) ?></p>
                <p><strong>Task Name:</strong> <?= htmlspecialchars($task['task_name']) ?></p>
                <p><strong>Description:</strong> <?= htmlspecialchars($task['description']) ?></p>
                <p><strong>Priority:</strong> <?= htmlspecialchars($task['priority']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($task['status']) ?></p>
                <p><strong>Start Date:</strong> <?= htmlspecialchars($task['start_date']) ?></p>
                <p><strong>End Date:</strong> <?= htmlspecialchars($task['end_date']) ?></p>
                <p><strong>Role:</strong> <?= htmlspecialchars($task['role']) ?></p>
                <form method="POST">
                    <button type="submit" name="action" value="accept">Accept Task</button>
                    <button type="submit" name="action" value="reject">Reject Task</button>
                </form>
            <?php endif; ?>
        </main>
    </div>

    <footer>
        <p>&copy; 2025 Task Allocator Pro. All rights reserved.</p>
        <p><a href="aboutus.php">About Us</a> | <a href="contact.php">Contact</a></p>
    </footer>
</body>
</html>































