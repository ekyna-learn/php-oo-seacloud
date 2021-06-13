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
use Service\Notifier;

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
    http_response_code(404);
    echo 'Server not found';
    exit;
}

/*if ($server->getState() === Server::STATE_READY) {
    http_response_code(200);
    exit;
}*/

$ip = sprintf('%s.%s.%s.%s', rand(1,255), rand(1,255), rand(1,255), rand(1,255));

$server
    ->setState(Server::STATE_READY)
    ->setIp($ip);

$serverManager = new ServerManager($connection);
$serverManager->persist($server);

$notifier = new Notifier(NOTIFY_EMAIL);
$notifier->notifyReady($server);

http_response_code(200);
