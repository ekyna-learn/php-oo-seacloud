<?php

declare(strict_types=1);

require __DIR__ . '/../../bootstrap.php';

/** @var PDO $connection */

use Entity\Server;
use Manager\ServerManager;
use Repository\UserRepository;
use Repository\ServerRepository;
use Repository\DataCenterRepository;
use Repository\DistributionRepository;
use Security\Firewall;

$userRepository = new UserRepository($connection);
$firewall = new Firewall($userRepository);
$firewall->denyAccessUnlessAuthenticated();

$dataCenterRepository = new DataCenterRepository($connection);
$distributionRepository = new DistributionRepository($connection);
$serverRepository = new ServerRepository(
    $connection,
    $userRepository,
    $dataCenterRepository,
    $distributionRepository
);

$server = $serverRepository->findOneByUserAndId($firewall->getUser(), (int)$_GET['id']);

if (is_null($server)) {
    http_response_code(404); // Not found
    echo 'Server not found';
    exit;
}

// Vérifié si la méthode HTTP est bien POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400); // Bad request
    exit;
}

$confirm = $_POST['confirm'] ?? '0';

if ('1' !== $confirm) {
    // L'internaute n'a pas coché la case de confirmation
    http_response_code(400); // Bad request
    exit;
}

// Supprime le serveur
$serverManager = new ServerManager($connection);
$serverManager->remove($server);

// Redirige vers le tableau de bord
redirect('/account/dashboard.php');
