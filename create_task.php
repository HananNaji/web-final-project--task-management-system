<?php
session_start();
include 'db.php.inc';
$user_role = $_SESSION['role'] ?? 'Guest';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SESSION['role'] !== 'Project Leader') {
    header('Location: error2.php'); 
    exit();
}

$stmt = $pdo->prepare("SELECT project_id, project_title FROM projects WHERE team_leader_id = :team_leader_id");
$stmt->execute(['team_leader_id' => $_SESSION['user_id']]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

$confirmationMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_name = $_POST['task_name'];
    $description = $_POST['description'];
    $project_id = $_POST['project_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $effort = $_POST['effort'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];

    try {
        $projectStmt = $pdo->prepare("SELECT start_date, end_date FROM projects WHERE project_id = :project_id");
        $projectStmt->execute(['project_id' => $project_id]);
        $project = $projectStmt->fetch(PDO::FETCH_ASSOC);

        if (!$project || $start_date < $project['start_date'] || $end_date > $project['end_date']) {
            throw new Exception("Task dates must align with project dates.");
        }

        $stmt = $pdo->prepare("
            INSERT INTO tasks (task_name, description, project_id, start_date, end_date, effort, status, priority)
            VALUES (:task_name, :description, :project_id, :start_date, :end_date, :effort, :status, :priority)
        ");
        $stmt->execute([
            'task_name' => $task_name,
            'description' => $description,
            'project_id' => $project_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'effort' => $effort,
            'status' => $status,
            'priority' => $priority,
        ]);

        $confirmationMessage = "Task [$task_name] successfully created.";
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

        <main>
            <?php if (!empty($confirmationMessage)): ?>
                <p style="color: green; font-weight: bold;"><?= htmlspecialchars($confirmationMessage) ?></p>
            <?php endif; ?>

            <form method="POST">
                <label for="task_name">Task Name:</label>
                <input type="text" id="task_name" name="task_name" required>

                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>

                <label for="project_id">Project:</label>
                <select id="project_id" name="project_id" required>
                    <option value="">-- Select Project --</option>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?= htmlspecialchars($project['project_id']) ?>">
                            <?= htmlspecialchars($project['project_title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required>

                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required>

                <label for="effort">Effort (Months):</label>
                <input type="number" id="effort" name="effort" required>

                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>

                <label for="priority">Priority:</label>
                <select id="priority" name="priority" required>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>

                <button type="submit">Create Task</button>
            </form>
        </main>
    </div>

    <footer>
        <p>&copy; 2025 Task Allocator Pro. All rights reserved.</p>
        <p><a href="aboutus.php">About Us</a> | <a href="contact.php">Contact</a></p>
    </footer>
</body>
</html>
