<?php
session_start();
include 'db.php.inc';
include 'User.php';
$errors = [];

$user = new User($pdo);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['username']) || strlen($_POST['username']) < 6 || strlen($_POST['username']) > 13) {
        $errors['username'] = "Username must be between 6 and 13 characters.";
    }

    if (empty($_POST['password']) || strlen($_POST['password']) < 8 || strlen($_POST['password']) > 12) {
        $errors['password'] = "Password must be between 8 and 12 characters.";
    }

    if ($_POST['password'] !== $_POST['password_confirmation']) {
        $errors['password_confirmation'] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $user->saveStep('step2', $_POST);
        header('Location: step3.php');
        exit();
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
        </nav>

        <main>
        <form method="POST" action="">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
    <?php if (isset($errors['username'])): ?>
        <p class="error"><?= $errors['username'] ?></p>
    <?php endif; ?>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <?php if (isset($errors['password'])): ?>
        <p class="error"><?= $errors['password'] ?></p>
    <?php endif; ?>

    <label for="password_confirmation">Confirm Password:</label>
    <input type="password" id="password_confirmation" name="password_confirmation" required>
    <?php if (isset($errors['password_confirmation'])): ?>
        <p class="error"><?= $errors['password_confirmation'] ?></p>
    <?php endif; ?>

    <button type="submit">Proceed to Confirmation</button>
</form>

        </main>
    </div>

    <footer>
        <p>&copy; 2025 Task Allocator Pro. All rights reserved.</p>
        <p><a href="aboutus.php">About Us</a> | <a href="contact.php">Contact</a></p>
    </footer>
</body>
</html>
