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

$server->setState(Server::STATE_STOPPED);

$serverManager = new ServerManager($connection);
$serverManager->persist($server);

redirect('/account/server-detail.php?id=' . $server->getId());
