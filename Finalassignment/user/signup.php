<?php
session_start();

/* Load admin portal DB config */
$root = realpath(dirname(__DIR__));
$configPath = $root . '/admin portal/config.php';
if (!is_file($configPath)) {
    echo "<pre style='color:#c00'>Database config not found at:\n" . htmlspecialchars($configPath) . "</pre>";
    exit;
}
require_once $configPath;

if (!isset($pdo) || !($pdo instanceof PDO)) {
    exit('Database connection ($pdo) was not initialized.');
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$success = '';
$name = '';
$email = '';
$password = '';

// Enable detailed PDO errors for debugging
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid form submission. Please refresh the page and try again.";
    } else {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Basic validation
        if ($name === '') {
            $error = 'Name is required.';
        } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'A valid email is required.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
        }

        if ($error === '') {
            try {
                // Check if table exists
                $tableCheck = $pdo->query("SHOW TABLES LIKE 'users'")->fetch();
                if (!$tableCheck) {
                    throw new Exception("Table `users` does not exist in the database.");
                }

                // Check for duplicate email
                $check = $pdo->prepare("SELECT 1 FROM `users` WHERE LOWER(`email`) = LOWER(?) LIMIT 1");
                $check->execute([$email]);

                if ($check->fetchColumn()) {
                    $error = "That email is already registered. Please log in instead.";
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (`name`, `email`, `password`) VALUES (?, ?, ?)");
                    $stmt->execute([$name, $email, $hash]);

                    $success = "Account created successfully! <a href='login.php'>Login here</a>";
                    $name = '';
                    $email = '';
                }
            } catch (PDOException $e) {
                $error = "Database error [{$e->getCode()}]: " . htmlspecialchars($e->getMessage());
            } catch (Exception $e) {
                $error = "Error: " . htmlspecialchars($e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Sign-Up</title>
<link rel="stylesheet" href="signup.css">
</head>
<body>
<div class="container login-box">
    <h2>User Sign-Up</h2>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label>Name:</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($name) ?>">

        <label>Email:</label>
        <input type="email" name="email" required placeholder="example@gmail.com" value="<?= htmlspecialchars($email) ?>">

        <label>Password:</label>
        <input type="password" name="password" required minlength="8" placeholder="At least 8 characters">

        <input type="submit" value="Sign Up" class="btn">

        <p>
            Already have an account?
            <a href="login.php" class="btn" style="display:inline-block; width:auto; padding:10px 14px; margin-left:6px;">Login</a>
        </p>
    </form>
</div>
</body>
</html>
