<?php
session_start();
include 'db.php.inc';
$user_role = $_SESSION['role'] ?? 'Guest'; 

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$stmt = $pdo->query("SELECT t.task_id, t.task_name, p.project_title, t.status, t.priority, t.start_date, t.end_date, t.progress_percentage
                     FROM tasks t
                     JOIN projects p ON t.project_id = p.project_id");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <a href="step1.php">Sign Up</a>
            <a href="login.php">Log in</a>
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
            <h2>Task List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Task ID</th>
                        <th>Task Name</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Progress (%)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?= htmlspecialchars($task['task_id']) ?></td>
                            <td><?= htmlspecialchars($task['task_name']) ?></td>
                            <td><?= htmlspecialchars($task['project_title']) ?></td>
                            <td><?= htmlspecialchars($task['status']) ?></td>
                            <td><?= htmlspecialchars($task['priority']) ?></td>
                            <td><?= htmlspecialchars($task['start_date']) ?></td>
                            <td><?= htmlspecialchars($task['end_date']) ?></td>
                            <td><?= htmlspecialchars($task['progress_percentage']) ?>%</td>
                            <td>
                                <a href="view_task_details.php?task_id=<?= htmlspecialchars($task['task_id']) ?>">View Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>

    <footer>
        <p>&copy; 2025 Task Allocator Pro. All rights reserved.</p>
        <p><a href="aboutus.php">About Us</a> | <a href="contact.php">Contact</a></p>
    </footer>
</body>
</html>

































