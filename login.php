<?php
session_start();
include 'db.php.inc';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; 
            header('Location: profile.php');
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
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
            <a href="login.php">Log in</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <nav class="sidebar">
          
           
        </nav>
        </nav>

        <main>
        <body>
        <h1>Login</h1>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <form method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</body>
        </main>
    </div>

    <footer>
        <p>&copy; 2025 Task Allocator Pro. All rights reserved.</p>
        <p><a href="aboutus.php">About Us</a> | <a href="contact.php">Contact</a></p>
    </footer>
</body>
</html>
