<?php
session_start();
include 'db.php.inc';
include 'Project.php';

$user_role = $_SESSION['role'] ?? 'Guest';

if (!isset($_SESSION['username'])) {
    header('Location: login.php'); 
    exit();
}

$project = new Project($pdo);
$errors = [];
$successMessage = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST['project_id']) || !preg_match('/^[A-Z]{4}-\d{5}$/', $_POST['project_id'])) {
            $errors['project_id'] = "Project ID must be in the format ABCD-12345.";
        }

        if (empty($_POST['project_title'])) {
            $errors['project_title'] = "Project Title is required.";
        }

        if (empty($_POST['project_description'])) {
            $errors['project_description'] = "Project Description is required.";
        }

        if (empty($_POST['customer_name'])) {
            $errors['customer_name'] = "Customer Name is required.";
        }

        if (empty($_POST['total_budget']) || $_POST['total_budget'] <= 0) {
            $errors['total_budget'] = "Total Budget must be a positive number.";
        }

        if (empty($_POST['start_date']) || empty($_POST['end_date'])) {
            $errors['dates'] = "Start Date and End Date are required.";
        } elseif ($_POST['start_date'] > $_POST['end_date']) {
            $errors['dates'] = "End Date must be later than Start Date.";
        }

        if (empty($_POST['documents_title'])) {
            $errors['documents_title'] = "Documents Title is required.";
        }

        if (!empty($_FILES['supporting_documents']['name'][0])) {
            foreach ($_FILES['supporting_documents']['name'] as $index => $name) {
                $fileSize = $_FILES['supporting_documents']['size'][$index];
                $fileType = pathinfo($name, PATHINFO_EXTENSION);

                if (!in_array(strtolower($fileType), ['pdf', 'docx', 'png', 'jpg'])) {
                    $errors['supporting_documents'] = "Only PDF, DOCX, PNG, and JPG files are allowed.";
                }

                if ($fileSize > 2 * 1024 * 1024) { 
                    $errors['supporting_documents'] = "Each file must be less than 2MB.";
                }
            }
        }

        if (empty($errors)) {
            $project->setProjectData($_POST);

            if (!empty($_FILES['supporting_documents']['name'][0])) {
                $project->addDocuments($_FILES['supporting_documents']);
            }

            $project->validateProject();
            $project->saveProject();

            $successMessage = "Project successfully added."; 
        }
    } catch (Exception $e) {
        $errors['general'] = $e->getMessage(); 
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
    <?php if (!empty($successMessage)): ?>
        <div class="success"><?= $successMessage ?></div>
    <?php endif; ?>

    <?php if (isset($errors['general'])): ?>
        <div class="error"><?= $errors['general'] ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="project_id">Project ID:</label>
        <input type="text" id="project_id" name="project_id" value="<?= htmlspecialchars($_POST['project_id'] ?? '') ?>" required pattern="[A-Z]{4}-\d{5}">
        <?php if (isset($errors['project_id'])): ?>
            <p class="error"><?= $errors['project_id'] ?></p>
        <?php endif; ?>

        <label for="project_title">Project Title:</label>
        <input type="text" id="project_title" name="project_title" value="<?= htmlspecialchars($_POST['project_title'] ?? '') ?>" required>
        <?php if (isset($errors['project_title'])): ?>
            <p class="error"><?= $errors['project_title'] ?></p>
        <?php endif; ?>

        <label for="project_description">Project Description:</label>
        <textarea id="project_description" name="project_description" required><?= htmlspecialchars($_POST['project_description'] ?? '') ?></textarea>
        <?php if (isset($errors['project_description'])): ?>
            <p class="error"><?= $errors['project_description'] ?></p>
        <?php endif; ?>

        <label for="customer_name">Customer Name:</label>
        <input type="text" id="customer_name" name="customer_name" value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>" required>
        <?php if (isset($errors['customer_name'])): ?>
            <p class="error"><?= $errors['customer_name'] ?></p>
        <?php endif; ?>

        <label for="total_budget">Total Budget:</label>
        <input type="number" id="total_budget" name="total_budget" value="<?= htmlspecialchars($_POST['total_budget'] ?? '') ?>" required>
        <?php if (isset($errors['total_budget'])): ?>
            <p class="error"><?= $errors['total_budget'] ?></p>
        <?php endif; ?>

        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>" required>
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>" required>
        <?php if (isset($errors['dates'])): ?>
            <p class="error"><?= $errors['dates'] ?></p>
        <?php endif; ?>

        <label for="documents_title">Documents Title:</label>
        <input type="text" id="documents_title" name="documents_title" value="<?= htmlspecialchars($_POST['documents_title'] ?? '') ?>" required>
        <?php if (isset($errors['documents_title'])): ?>
            <p class="error"><?= $errors['documents_title'] ?></p>
        <?php endif; ?>

        <label for="supporting_documents">Supporting Documents:</label>
        <input type="file" id="supporting_documents" name="supporting_documents[]" multiple>
        <?php if (isset($errors['supporting_documents'])): ?>
            <p class="error"><?= $errors['supporting_documents'] ?></p>
        <?php endif; ?>

        <button type="submit">Add Project</button>
    </form>
</main>

    </div>

    <footer>
        <p>&copy; 2025 Task Allocator Pro. All rights reserved.</p>
        <p><a href="aboutus.php">About Us</a> | <a href="contact.php">Contact</a></p>
    </footer>
</body>
</html>
