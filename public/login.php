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
    $result = $user->login($_POST['username'], $_POST['password']);

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
    <title>Login</title>
</head>
<body>
<div class="container">
    <h1>Login</h1>

    <?php if ($message): ?>
        <div><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="./login.php">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>
</body>
</html>