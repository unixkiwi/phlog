<?php
declare(strict_types=1);

use classes\Session;

require_once __DIR__ . '/vendor/autoload.php';

$session = Session::getInstance();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Phlog</title>
</head>
<body>
<main>
    <section>
        <h1>Welcome to Phlog</h1>
        <h3>A little blog written in purely in PHP!</h3>
    </section>

    <section>
        <h2>Account</h2>
        <?php if ($session->isLoggedIn()) { ?>
                <p><?php echo $session->getUsername() ?></p>
                <p><a href="public/logout.php">Logout</a></p>
        <?php } else { ?>
                <p><a href="public/login.php">Login</a></p>
                <p><a href="public/register.php">Register</a></p>
        <?php } ?>
    </section>
</main>
</body>
</html>