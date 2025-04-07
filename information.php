<?php
session_start();
include 'db.php.inc';
$user_role = $_SESSION['role'] ?? 'Guest';

if (!isset($_GET['user_id'])) {
    echo "Error: User ID not provided.";
    exit();
}

$user_id = $_GET['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Error: User not found.";
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
            <h1>User Profile</h1>
            <div>
                <img src="images/<?php echo htmlspecialchars($user['photo']); ?>" alt="Profile Picture">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>

                <?php
                $address = json_decode($user['address'], true);
                if ($address) {
                    echo "<p><strong>Address:</strong> ";
                    echo "Flat/House No: " . htmlspecialchars($address['flat']) . ", ";
                    echo "Street: " . htmlspecialchars($address['street']) . ", ";
                    echo "City: " . htmlspecialchars($address['city']) . ", ";
                    echo "Country: " . htmlspecialchars($address['country']);
                    echo "</p>";
                } else {
                    echo "<p><strong>Address:</strong> Not Available</p>";
                }
                ?>

                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['dob']); ?></p>
                <p><strong>ID Number:</strong> <?php echo htmlspecialchars($user['id_number']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Telephone:</strong> <?php echo htmlspecialchars($user['telephone']); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
                <p><strong>Qualification:</strong> <?php echo htmlspecialchars($user['qualification']); ?></p>
                <p><strong>Skills:</strong> <?php echo htmlspecialchars($user['skills']); ?></p>
            </div>
        </main>
    </div>

    <footer>
        <p>&copy; 2025 Task Allocator Pro. All rights reserved.</p>
        <p><a href="aboutus.php">About Us</a> | <a href="contact.php">Contact</a></p>
    </footer>
</body>
</html>
