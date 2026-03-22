<?php


// Only staff can access
if (empty($_SESSION['staff'])) {
    header("Location: staff_login.php");
    exit;
}

require_once "config.php";

// Fetch staff list (READ-ONLY)
$staffList = $pdo->query("SELECT id, name, email FROM users WHERE role = 'staff' ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch student list (READ-ONLY)
$studentList = $pdo->query("SELECT id, name, email, created_at FROM users WHERE role = 'student' ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="staff_dashboard.css">
</head>
<body>

<header class="main-header">
    <h1>👨‍🏫 Staff Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['staff']['name']) ?> | <a href="staff_logout.php">Logout</a></p>
</header>

<div class="container">
    
    <!-- Staff List (VIEW ONLY) -->
    <section class="card">
        <h2>👥 Staff Members</h2>
        <p class="note">🔒 View only — Contact admin for changes</p>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <!-- ❌ NO Actions column -->
            </tr>
            <?php foreach ($staffList as $s): ?>
            <tr>
                <td><?= (int)$s['id'] ?></td>
                <td><?= htmlspecialchars($s['name']) ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </section>
    
    <!-- Add Student Button -->
    <section class="card">
        <h2>➕ Add New Student</h2>
        <a href="staff_add_student.php" class="btn btn-success">Add Student →</a>
    </section>
    
    <!-- Student List (VIEW ONLY) -->
    <section class="card">
        <h2>🎓 Registered Students</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Joined</th>
                <!-- ❌ NO Actions column -->
            </tr>
            <?php foreach ($studentList as $stu): ?>
            <tr>
                <td><?= (int)$stu['id'] ?></td>
                <td><?= htmlspecialchars($stu['name']) ?></td>
                <td><?= htmlspecialchars($stu['email']) ?></td>
                <td><?= date('M Y', strtotime($stu['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </section>
    
    <p><a href="../frontpage/home.php">← Back to Home</a></p>
    
</div>

</body>
</html>