<?php
include 'db.php.inc';

$stmt = $pdo->query("SELECT * FROM projects");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects List</title>
</head>
<body>
    <h1>Projects List</h1>
    <table>
        <thead>
            <tr>
                <th>Project ID</th>
                <th>Title</th>
                <th>Customer</th>
                <th>Budget</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?= htmlspecialchars($project['project_id']) ?></td>
                    <td><?= htmlspecialchars($project['project_title']) ?></td>
                    <td><?= htmlspecialchars($project['customer_name']) ?></td>
                    <td><?= htmlspecialchars($project['total_budget']) ?></td>
                    <td>
                        <a href="project_details.php?project_id=<?= htmlspecialchars($project['project_id']) ?>">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
