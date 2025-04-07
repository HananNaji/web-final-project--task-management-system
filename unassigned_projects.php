<?php
session_start();
include 'db.php.inc';
$user_role = $_SESSION['role'] ?? 'Guest'; 
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); 
    exit();
}
$stmt = $pdo->prepare("SELECT project_id, project_title, start_date, end_date FROM projects WHERE team_leader_id IS NULL ORDER BY start_date ASC");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  

        <main>
        <h2>Unassigned Projects</h2>
        <table>
            <thead>
                <tr>
                    <th>Project ID</th>
                    <th>Project Title</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?= htmlspecialchars($project['project_id']) ?></td>
                    <td><?= htmlspecialchars($project['project_title']) ?></td>
                    <td><?= htmlspecialchars($project['start_date']) ?></td>
                    <td><?= htmlspecialchars($project['end_date']) ?></td>
                    <td>
                        <a href="allocate_team_leader.php?project_id=<?= htmlspecialchars($project['project_id']) ?>">Allocate Team Leader</a>
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


























