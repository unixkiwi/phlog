<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use classes\Session;

Session::getInstance()->logout();

header('Location: ../index.php');
exit();
