<?php
session_start();
include 'db.php.inc';
$user_role = $_SESSION['role'] ?? 'Guest';

if (!isset($_SESSION['username'])) {
    header('Location: login.php'); 
    exit();
}

if (!isset($_GET['project_id'])) {
    echo "Error: Project ID is missing.";
    exit();
}

$project_id = $_GET['project_id'];

$stmt = $pdo->prepare("SELECT * FROM projects WHERE project_id = :project_id");
$stmt->execute(['project_id' => $project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    echo "Error: Project not found.";
    exit();
}

$leadersStmt = $pdo->query("SELECT user_id, name FROM users WHERE role = 'Project Leader'");
$teamLeaders = $leadersStmt->fetchAll(PDO::FETCH_ASSOC);

$successMessage = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_leader_id = $_POST['team_leader_id'];

    if (empty($team_leader_id)) {
        echo "Error: Please select a Team Leader.";
    } else {
        $updateStmt = $pdo->prepare("UPDATE projects SET team_leader_id = :team_leader_id WHERE project_id = :project_id");
        $updateStmt->execute(['team_leader_id' => $team_leader_id, 'project_id' => $project_id]);

        $successMessage = "Team Leader successfully allocated to Project $project_id.";
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
            <h2>Project Details</h2>

            <?php if (!empty($successMessage)): ?>
                <div class="success"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>

            <p><strong>Project ID:</strong> <?= htmlspecialchars($project['project_id']) ?></p>
            <p><strong>Project Title:</strong> <?= htmlspecialchars($project['project_title']) ?></p>
            <p><strong>Project Description:</strong> <?= htmlspecialchars($project['project_description']) ?></p>
            <p><strong>Customer Name:</strong> <?= htmlspecialchars($project['customer_name']) ?></p>
            <p><strong>Total Budget:</strong> <?= htmlspecialchars($project['total_budget']) ?></p>
            <p><strong>Start Date:</strong> <?= htmlspecialchars($project['start_date']) ?></p>
            <p><strong>End Date:</strong> <?= htmlspecialchars($project['end_date']) ?></p>

            <form method="POST">
                <label for="team_leader_id">Select Team Leader:</label>
                <select id="team_leader_id" name="team_leader_id" required>
                    <option value="">-- Select Team Leader --</option>
                    <?php foreach ($teamLeaders as $leader): ?>
                        <option value="<?= htmlspecialchars($leader['user_id']) ?>">
                            <?= htmlspecialchars($leader['name']) ?> - <?= htmlspecialchars($leader['user_id']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Confirm Allocation</button>
            </form>
        </main>
    </div>

    <footer>
        <p>&copy; 2025 Task Allocator Pro. All rights reserved.</p>
        <p><a href="aboutus.php">About Us</a> | <a href="contact.php">Contact</a></p>
    </footer>
</body>
</html>
