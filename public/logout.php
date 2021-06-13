<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

/** @var PDO $connection */

use Repository\UserRepository;
use Security\Firewall;

$repository = new UserRepository($connection);
$firewall = new Firewall($repository);

$firewall->logout();

redirect('/sign-in.php');
