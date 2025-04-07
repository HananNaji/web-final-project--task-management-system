
<?php
session_start();
include 'db.php.inc';
include 'User.php';

$user = new User($pdo);

$errors = []; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['name'])) {
        $errors['name'] = "Name cannot be empty.";
    }

    if (empty($_POST['address']['flat']) || empty($_POST['address']['street']) || empty($_POST['address']['city']) || empty($_POST['address']['country'])) {
        $errors['address'] = "Complete address is required.";
    }

    if (empty($_POST['dob']) || !strtotime($_POST['dob'])) {
        $errors['dob'] = "Invalid date of birth.";
    }

    if (empty($_POST['id_number']) || !ctype_digit($_POST['id_number'])) {
        $errors['id_number'] = "ID Number must be numeric.";
    }

    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email address.";
    }

    if (empty($_POST['telephone'])) {
        $errors['telephone'] = "Telephone cannot be empty.";
    }

    if (empty($_POST['qualification'])) {
        $errors['qualification'] = "Qualification cannot be empty.";
    }

    if (empty($_POST['skills'])) {
        $errors['skills'] = "Skills cannot be empty.";
    }

    if (empty($_POST['role'])) {
        $errors['role'] = "Role is required.";
    }

    if (empty($errors)) {
        $user->saveStep('step1', $_POST); 
        header('Location: step2.php');
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
    <h1>Sign Up</h1>
    <h2>User Information Form</h2>

    <section>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        <?php if (isset($errors['name'])): ?>
            <p class="error"><?= $errors['name'] ?></p>
        <?php endif; ?>
    </section>

    <section>


        <fieldset>
            <legend>Address:</legend>
            <label for="flat">Flat/House No:</label>
            <input type="text" id="flat" name="address[flat]" value="<?= htmlspecialchars($_POST['address']['flat'] ?? '') ?>" required>
            <label for="street">Street:</label>
            <input type="text" id="street" name="address[street]" value="<?= htmlspecialchars($_POST['address']['street'] ?? '') ?>" required>
            <label for="city">City:</label>
            <input type="text" id="city" name="address[city]" value="<?= htmlspecialchars($_POST['address']['city'] ?? '') ?>" required>
            <label for="country">Country:</label>
            <input type="text" id="country" name="address[country]" value="<?= htmlspecialchars($_POST['address']['country'] ?? '') ?>" required>
            <?php if (isset($errors['address'])): ?>
                <p class="error"><?= $errors['address'] ?></p>
            <?php endif; ?>
        </fieldset>
    </section>
    <section>
    <label for="dob">Date of Birth:</label>
    <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>" required>
    <?php if (isset($errors['dob'])): ?>
        <p class="error"><?= $errors['dob'] ?></p>
    <?php endif; ?>
</section>

<section>
    <label for="id_number">ID Number:</label>
    <input type="text" id="id_number" name="id_number" pattern="\d+" value="<?= htmlspecialchars($_POST['id_number'] ?? '') ?>" required>
    <?php if (isset($errors['id_number'])): ?>
        <p class="error"><?= $errors['id_number'] ?></p>
    <?php endif; ?>
</section>

<section>
    <label for="email">Email Address:</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
    <?php if (isset($errors['email'])): ?>
        <p class="error"><?= $errors['email'] ?></p>
    <?php endif; ?>
</section>

<section>
    <label for="telephone">Telephone:</label>
    <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>" required>
    <?php if (isset($errors['telephone'])): ?>
        <p class="error"><?= $errors['telephone'] ?></p>
    <?php endif; ?>
</section>

<section>
    <label for="role">Role:</label>
    <select id="role" name="role" required>
        <option value="">Select Role</option>
        <option value="Manager" <?= (isset($_POST['role']) && $_POST['role'] === 'Manager') ? 'selected' : '' ?>>Manager</option>
        <option value="Project Leader" <?= (isset($_POST['role']) && $_POST['role'] === 'Project Leader') ? 'selected' : '' ?>>Project Leader</option>
        <option value="Team Member" <?= (isset($_POST['role']) && $_POST['role'] === 'Team Member') ? 'selected' : '' ?>>Team Member</option>
    </select>
    <?php if (isset($errors['role'])): ?>
        <p class="error"><?= $errors['role'] ?></p>
    <?php endif; ?>
</section>

<section>
    <label for="qualification">Qualification:</label>
    <input type="text" id="qualification" name="qualification" value="<?= htmlspecialchars($_POST['qualification'] ?? '') ?>" required>
    <?php if (isset($errors['qualification'])): ?>
        <p class="error"><?= $errors['qualification'] ?></p>
    <?php endif; ?>
</section>

<section>
    <label for="skills">Skills:</label>
    <input type="text" id="skills" name="skills" value="<?= htmlspecialchars($_POST['skills'] ?? '') ?>" required>
    <?php if (isset($errors['skills'])): ?>
        <p class="error"><?= $errors['skills'] ?></p>
    <?php endif; ?>
</section>

            <section>
                <button type="submit">Proceed to Step 2</button>
            </section>
        </form>
    </main>
    </div>

    <footer>
        <p>&copy; 2025 Task Allocator Pro. All rights reserved.</p>
        <p><a href="aboutus.php">About Us</a> | <a href="contact.php">Contact</a></p>
    </footer>
</body>
</html>
