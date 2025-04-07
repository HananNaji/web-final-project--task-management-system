<?php
session_start();
include 'db.php.inc';
$user_role = $_SESSION['role'] ?? 'Guest';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Project Leader') {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['task_id']) || empty($_GET['task_id'])) {
    echo "Error: Task ID is missing.";
    exit();
}

$task_id = $_GET['task_id'];

$stmt = $pdo->prepare("SELECT * FROM tasks WHERE task_id = :task_id");
$stmt->execute(['task_id' => $task_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    echo "Error: Task not found.";
    exit();
}

// جلب أعضاء الفريق
$teamStmt = $pdo->query("SELECT user_id, name FROM users WHERE role = 'Team Member'");
$teamMembers = $teamStmt->fetchAll(PDO::FETCH_ASSOC);

$confirmationMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_member_id = $_POST['team_member_id'];
    $role = $_POST['role'];
    $contribution_percentage = $_POST['contribution_percentage'];
    $start_date = $_POST['start_date'];

    try {
        // التحقق من القيم
        if (empty($team_member_id) || empty($role) || empty($contribution_percentage)) {
            throw new Exception("All fields are required.");
        }

        if ($contribution_percentage <= 0 || $contribution_percentage > 100) {
            throw new Exception("Contribution percentage must be between 1 and 100.");
        }

        if ($start_date < $task['start_date'] || $start_date > $task['end_date']) {
            throw new Exception("Start date must be within the task's date range.");
        }

        // إدخال البيانات في قاعدة البيانات
        $stmt = $pdo->prepare("
            INSERT INTO task_team_members (task_id, user_id, role, contribution_percentage, start_date)
            VALUES (:task_id, :user_id, :role, :contribution_percentage, :start_date)
        ");
        $stmt->execute([
            'task_id' => $task_id,
            'user_id' => $team_member_id,
            'role' => $role,
            'contribution_percentage' => $contribution_percentage,
            'start_date' => $start_date,
        ]);

        $confirmationMessage = "Team member successfully assigned to Task [{$task['task_name']}] as {$role}.";
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
    <title>Assign Team Members</title>
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
            <h2>Assign Team Members to Task</h2>
            <p><strong>Task ID:</strong> <?= htmlspecialchars($task['task_id']) ?></p>
            <p><strong>Task Name:</strong> <?= htmlspecialchars($task['task_name']) ?></p>
            <p><strong>Start Date:</strong> <?= htmlspecialchars($task['start_date']) ?></p>
            <p><strong>End Date:</strong> <?= htmlspecialchars($task['end_date']) ?></p>

            <?php if (!empty($confirmationMessage)): ?>
                <div class="<?= strpos($confirmationMessage, 'Error:') === 0 ? 'error' : 'success' ?>">
                    <?= htmlspecialchars($confirmationMessage) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <label for="team_member_id">Team Member:</label>
                <select id="team_member_id" name="team_member_id" required>
                    <option value="">-- Select Team Member --</option>
                    <?php foreach ($teamMembers as $member): ?>
                        <option value="<?= htmlspecialchars($member['user_id']) ?>">
                            <?= htmlspecialchars($member['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="Developer">Developer</option>
                    <option value="Designer">Designer</option>
                    <option value="Tester">Tester</option>
                    <option value="Analyst">Analyst</option>
                    <option value="Support">Support</option>
                </select>

                <label for="contribution_percentage">Contribution Percentage:</label>
                <input type="number" id="contribution_percentage" name="contribution_percentage" required>

                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?= date('Y-m-d') ?>" required>

                <button type="submit" name="action" value="add_more">Add Another Team Member</button>
                <button type="submit" name="action" value="finish_allocation">Finish Allocation</button>
            </form>
        </main>
    </div>

    <footer>
        <p>&copy; 2025 Task Allocator Pro. All rights reserved.</p>
        <p><a href="aboutus.php">About Us</a> | <a href="contact.php">Contact</a></p>
    </footer>
</body>
</html>
