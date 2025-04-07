<?php
session_start();
include 'db.php.inc';
$user_role = $_SESSION['role'] ?? 'Guest'; 

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Team Member') {
    header('Location: login.php');
    exit();
}

$query = "SELECT task_id, task_name, project_id, progress_percentage, status FROM tasks";
$params = [];
$conditions = [];

if (!empty($_GET['task_id'])) {
    $conditions[] = "task_id = :task_id";
    $params['task_id'] = $_GET['task_id'];
}

if (!empty($_GET['task_name'])) {
    $conditions[] = "task_name LIKE :task_name";
    $params['task_name'] = '%' . $_GET['task_name'] . '%';
}

if (!empty($_GET['project_name'])) {
    $conditions[] = "project_id = :project_id"; 
    $params['project_id'] = $_GET['project_name'];
}

if ($conditions) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
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
            <h2>Search Tasks</h2>
            <form method="GET" action="search_tasks.php">
                <label for="task_id">Task ID:</label>
                <input type="text" id="task_id" name="task_id">

                <label for="task_name">Task Name:</label>
                <input type="text" id="task_name" name="task_name">

                <label for="project_name">Project Name:</label>
                <input type="text" id="project_name" name="project_name">

                <button type="submit">Search</button>
            </form>

            <h3>Task Results</h3>
            <table>
                <thead>
                    <tr>
                        <th>Task ID</th>
                        <th>Task Name</th>
                        <th>Project ID</th>
                        <th>Progress (%)</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?= htmlspecialchars($task['task_id']) ?></td>
                            <td><?= htmlspecialchars($task['task_name']) ?></td>
                            <td><?= htmlspecialchars($task['project_id']) ?></td>
                            <td><?= htmlspecialchars($task['progress_percentage']) ?>%</td>
                            <td><?= htmlspecialchars($task['status']) ?></td>
                            <td>
                                <a href="update_task.php?task_id=<?= htmlspecialchars($task['task_id']) ?>">Update</a>
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

































