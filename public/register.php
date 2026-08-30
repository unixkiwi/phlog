<?php
declare(strict_types=1);

use classes\User;

session_start();
require_once '../classes/Database.php';
require_once '../classes/User.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();
    $result = $user->register($_POST['username'], $_POST['password']);

    var_dump($result);

    if ($result->success) {
    } else {
        $message = $result->message;
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