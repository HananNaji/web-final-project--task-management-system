
<?php
session_start();

include 'db.php.inc';
$user_role = $_SESSION['role'] ?? 'Guest'; 

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
$stmt = $pdo->prepare("SELECT photo FROM users WHERE username = :username");
$stmt->execute(['username' => $_SESSION['username']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = $pdo->query("SELECT project_id, project_title FROM projects");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($user && !empty($user['photo'])) {
    $_SESSION['photo'] = $user['photo'];
} else {
    echo "Error: Unable to fetch photo from database.";
    exit();
}

if (!isset($_SESSION['photo']) || empty($_SESSION['photo'])) {
    echo "Error: Photo is not set in session.";
    exit();
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
        <div>


                <strong>your image:</strong> 






                <a href="information.php?user_id=<?php echo htmlspecialchars($_SESSION['user_id']); ?>">

<img src="./images/<?php echo htmlspecialchars($_SESSION['photo']); ?>" alt="Profile Picture">
                </a>
                <!-- رابط اسم المستخدم -->
                <p>
                    <strong>Username:</strong> 
                    <a href="information.php?user_id=<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                </p>
            </div>
    </main>
    </div>

    <!-- الفوتر -->
    <footer>
        <p>&copy; 2025 Task Allocator Pro. All rights reserved.</p>
        <p><a href="aboutus.php">About Us</a> | <a href="contact.php">Contact</a></p>
    </footer>
</body>
</html>
