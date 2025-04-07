<?php
session_start();
include 'db.php.inc';
$user_role = $_SESSION['role'] ?? 'Guest'; 

if (!isset($_GET['task_id'])) {
    die("Error: Task ID is missing.");
}

$task_id = $_GET['task_id'];

$taskStmt = $pdo->prepare("
    SELECT t.task_id, t.task_name, t.description, p.project_title, t.start_date, t.end_date, t.progress_percentage, t.status, t.priority
    FROM tasks t
    JOIN projects p ON t.project_id = p.project_id
    WHERE t.task_id = :task_id
");
$taskStmt->execute(['task_id' => $task_id]);
$task = $taskStmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    die("Error: Task not found.");
}
$teamStmt = $pdo->prepare("
    SELECT u.photo, u.user_id, u.name, tm.start_date, tm.role, tm.contribution_percentage
    FROM task_team_members tm
    JOIN users u ON tm.user_id = u.user_id
    WHERE tm.task_id = :task_id
");

$teamStmt->execute(['task_id' => $task_id]);
$teamMembers = $teamStmt->fetchAll(PDO::FETCH_ASSOC);
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
            <h2>Task Details</h2>
            <div class="task-details">
                <h3>Task Details</h3>
                <p><strong>Task ID:</strong> <?= htmlspecialchars($task['task_id']) ?></p>
                <p><strong>Task Name:</strong> <?= htmlspecialchars($task['task_name']) ?></p>
                <p><strong>Description:</strong> <?= htmlspecialchars($task['description']) ?></p>
                <p><strong>Project:</strong> <?= htmlspecialchars($task['project_title']) ?></p>
                <p><strong>Start Date:</strong> <?= htmlspecialchars($task['start_date']) ?></p>
                <p><strong>End Date:</strong> <?= htmlspecialchars($task['end_date']) ?></p>
                <p><strong>Progress:</strong> <?= htmlspecialchars($task['progress_percentage']) ?>%</p>
                <p><strong>Status:</strong> <?= htmlspecialchars($task['status']) ?></p>
                <p><strong>Priority:</strong> <?= htmlspecialchars($task['priority']) ?></p>
            </div>
            <div class="team-members">
                <h3>Team Members</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Member ID</th>
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>Role</th>
                            <th>Effort Allocated (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teamMembers as $member): ?>
                            <tr>
                            <td>
    <img src="images/<?= htmlspecialchars($member['photo']) ?>" alt="Photo" class="team-photo" onerror="this.src='images/default.jpg';">
</td>
                            <td><?= htmlspecialchars($member['user_id']) ?></td>
                                <td><?= htmlspecialchars($member['name']) ?></td>
                                <td><?= htmlspecialchars($member['start_date']) ?></td>
                                <td><?= htmlspecialchars($member['role']) ?></td>
                                <td><?= htmlspecialchars($member['contribution_percentage']) ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <footer>
        <p>&copy; 2025 Task Allocator Pro. All rights reserved.</p>
        <p><a href="aboutus.php">About Us</a> | <a href="contact.php">Contact</a></p>
    </footer>
</body>
</html>



















































