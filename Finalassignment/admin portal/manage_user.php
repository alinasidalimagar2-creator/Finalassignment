<?php
include 'config.php';
include 'auth.php';

// (Optional) Delete via query param, matching the pattern in staff.php's header code.
// Note: Your staff.php currently links to delete_staff.php, so this block won't run unless you link to manage_user.php?delete=ID
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM `users` WHERE `id` = ?");
    $stmt->execute([$_GET['delete']]);
}

// Fetch all users (from singular table `user`)
$users = $pdo->query("SELECT `id`, `name`, `email` FROM `users` ORDER BY `name`")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="staff.css">
</head>
<body>
<h2>User Management</h2>

<a href="dashboard.php" class="button">← Back</a>
<span style="margin: 0 10px; color: #fff;">|</span>


<table>
    <tr>
        <th>User ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($users as $u): ?>
    <tr>
        <td><?= htmlspecialchars($u['id']); ?></td>
        <td><?= htmlspecialchars($u['name']); ?></td>
        <td><?= htmlspecialchars($u['email']); ?></td>
        <td>
            
            <a href="delete_user.php?id=<?= (int)$u['id']; ?>">Delete</a>
            <!-- If you prefer inline delete like staff.php's top block:
                 <a href="manage_user.php?delete=<?= (int)$u['id']; ?>">Delete</a>
            -->
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
