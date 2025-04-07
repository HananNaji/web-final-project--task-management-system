<?php
session_start();
include 'db.php.inc';
$user_role = $_SESSION['role'] ?? 'Guest'; 

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Team Member') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT 
        t.task_id,
        t.task_name,
        t.start_date,
        p.project_title
    FROM 
        task_team_members tm
    JOIN 
        tasks t ON tm.task_id = t.task_id
    JOIN 
        projects p ON t.project_id = p.project_id
    WHERE 
        tm.user_id = :user_id
");
$stmt->execute(['user_id' => $user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Tasks</title>
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
        <main>
            <h2>Assigned Tasks</h2>
            <?php if (count($tasks) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Task ID</th>
                            <th>Task Name</th>
                            <th>Project Name</th>
                            <th>Start Date</th>
                            <th>Confirm</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td><?= htmlspecialchars($task['task_id']) ?></td>
                                <td><?= htmlspecialchars($task['task_name']) ?></td>
                                <td><?= htmlspecialchars($task['project_title']) ?></td>
                                <td><?= htmlspecialchars($task['start_date']) ?></td>
                                <td>
                                    <a href="task_confirmation.php?task_id=<?= htmlspecialchars($task['task_id']) ?>">View & Respond</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No tasks assigned to you yet.</p>
            <?php endif; ?>
        </main>
    </div>
    <footer>
        <p>&copy; 2025 Task Allocator Pro. All rights reserved.</p>
    </footer>
</body>
</html>
