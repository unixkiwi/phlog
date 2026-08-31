<?php
declare(strict_types=1);

use classes\User;

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../classes/Database.php';
require_once '../classes/User.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();
    $result = $user->register($_POST['username'], $_POST['password']);

    $message = $result->message;

    if ($result->success) {
        header("Location: ../index.php");
        die();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
</head>
<body>
<div class="container">
    <h1>Register</h1>

    <?php if ($message): ?>
        <div><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="./register.php">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Log-In</a></p>
</div>
</body>
</html>