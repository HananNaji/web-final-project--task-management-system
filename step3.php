<?php
session_start();
include 'db.php.inc';
include 'User.php';

$user = new User($pdo);

if (!$user->validateSteps()) {
    echo "Error: Session data not available. Please go back and complete the steps.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $user->mergeSteps(); 
        $user_id = $user->registerUser(); 
        
        session_destroy();
        header('Location: submit.php');
        exit();
    } catch (Exception $e) {
        $error_message = $e->getMessage(); 
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
      
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <nav class="sidebar">
         
        </nav>
        <main>
    <h1>Confirmation</h1>
    <?php if (!empty($error_message)): ?>
        <p class="error"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>
    <p>Review your details before submission:</p>
    <table>
        <thead>
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Name</td><td><?= htmlspecialchars($_SESSION['step1']['name'] ?? 'Not Provided') ?></td></tr>
            <tr><td>Address</td>
                <td>
                    <?= htmlspecialchars($_SESSION['step1']['address']['flat'] ?? 'Not Provided') . ', ' .
                        htmlspecialchars($_SESSION['step1']['address']['street'] ?? 'Not Provided') . ', ' .
                        htmlspecialchars($_SESSION['step1']['address']['city'] ?? 'Not Provided') . ', ' .
                        htmlspecialchars($_SESSION['step1']['address']['country'] ?? 'Not Provided') ?>
                </td>
            </tr>
            <tr><td>Date of Birth</td><td><?= htmlspecialchars($_SESSION['step1']['dob'] ?? 'Not Provided') ?></td></tr>
            <tr><td>ID Number</td><td><?= htmlspecialchars($_SESSION['step1']['id_number'] ?? 'Not Provided') ?></td></tr>
            <tr><td>Email Address</td><td><?= htmlspecialchars($_SESSION['step1']['email'] ?? 'Not Provided') ?></td></tr>
            <tr><td>Telephone</td><td><?= htmlspecialchars($_SESSION['step1']['telephone'] ?? 'Not Provided') ?></td></tr>
            <tr><td>Role</td><td><?= htmlspecialchars($_SESSION['step1']['role'] ?? 'Not Provided') ?></td></tr>
            <tr><td>Qualification</td><td><?= htmlspecialchars($_SESSION['step1']['qualification'] ?? 'Not Provided') ?></td></tr>
            <tr><td>Skills</td><td><?= htmlspecialchars($_SESSION['step1']['skills'] ?? 'Not Provided') ?></td></tr>
            <tr><td>Username</td><td><?= htmlspecialchars($_SESSION['step2']['username'] ?? 'Not Provided') ?></td></tr>
        </tbody>
    </table>
    
    <form method="POST" action="">
        <button type="submit" class="btn-confirm">Confirm</button>
    </form>
</main>

    </div>

    <footer>
        <p>&copy; 2025 Task Allocator Pro. All rights reserved.</p>
        <p><a href="aboutus.php">About Us</a> | <a href="contact.php">Contact</a></p>
    </footer>
</body>
</html>
