<?php

declare(strict_types=1);

use Entity\DataCenter;
use Entity\Distribution;
use Manager\DataCenterManager;
use Manager\DistributionManager;

require 'bootstrap.php';

/** @var PDO $connection */

$tableAttributes = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci';

$schema = [
    'CREATE TABLE `data_center` (
      `id` int UNSIGNED NOT NULL,
      `name` varchar(64) NOT NULL,
      `code` varchar(2) NOT NULL
    ) ' . $tableAttributes,

    'CREATE TABLE `distribution` (
      `id` int UNSIGNED NOT NULL,
      `name` varchar(64) NOT NULL,
      `code` varchar(16) NOT NULL
    ) ' . $tableAttributes,

    'CREATE TABLE `server` (
      `id` int UNSIGNED NOT NULL,
      `user_id` int UNSIGNED NOT NULL,
      `location_id` int UNSIGNED NOT NULL,
      `distribution_id` int UNSIGNED NOT NULL,
      `name` varchar(64) NOT NULL,
      `ip` varchar(16) DEFAULT NULL,
      `state` int NOT NULL,
      `cpu` int NOT NULL,
      `ram` int NOT NULL
    ) ' . $tableAttributes,

    'CREATE TABLE `user` (
      `id` int UNSIGNED NOT NULL,
      `email` varchar(255) NOT NULL,
      `password` varchar(255) NOT NULL
    ) ' . $tableAttributes,

    'ALTER TABLE `data_center` ADD PRIMARY KEY (`id`)',

    'ALTER TABLE `distribution` ADD PRIMARY KEY (`id`)',

    'ALTER TABLE `server`
      ADD PRIMARY KEY (`id`),
      ADD KEY `user_id` (`user_id`),
      ADD KEY `location_id` (`location_id`),
      ADD KEY `distribution_id` (`distribution_id`)',

    'ALTER TABLE `user` ADD PRIMARY KEY (`id`)',

    'ALTER TABLE `data_center` MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT',

    'ALTER TABLE `distribution` MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT',

    'ALTER TABLE `server` MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT',

    'ALTER TABLE `user` MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT',

    'ALTER TABLE `server`
      ADD CONSTRAINT `fk_server_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
      ADD CONSTRAINT `fk_server_data_center` FOREIGN KEY (`location_id`) REFERENCES `data_center` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
      ADD CONSTRAINT `fk_server_distribution` FOREIGN KEY (`distribution_id`) REFERENCES `distribution` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE',
];

foreach ($schema as $query) {
    $connection->query($query);
}

$manager = new DataCenterManager($connection);

$data = [
    'New York'      => 'NY',
    'San Francisco' => 'SF',
    'Amsterdam'     => 'AM',
    'Singapore'     => 'SI',
    'London'        => 'LO',
    'Frankfurt'     => 'FR',
    'Toronto'       => 'TO',
    'Bangalore'     => 'BA',
];

foreach ($data as $name => $code) {
    $dataCenter = new DataCenter();
    $dataCenter
        ->setName($name)
        ->setCode($code);

    $manager->persist($dataCenter);
}

$data = [
    'Ubuntu 20.04 (LTS) x64' => 'Ubuntu',
    'FreeBSD 12.2 x64'       => 'FreeBSD',
    'Fedora 34 x64'          => 'Fedora',
    'Debian 10 x64'          => 'Debian',
    'CentOS 8.3 x64'         => 'CentOS',
];

$manager = new DistributionManager($connection);

foreach ($data as $name => $code) {
    $distribution = new Distribution();
    $distribution
        ->setName($name)
        ->setCode($code);

    $manager->persist($distribution);
}
