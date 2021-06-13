<?php

declare(strict_types=1);

require __DIR__ . '/../../bootstrap.php';

/** @var PDO $connection */

use Repository\DataCenterRepository;
use Repository\DistributionRepository;
use Repository\ServerRepository;
use Repository\UserRepository;
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

$servers = $serverRepository->findByUser($firewall->getUser());

?><!DOCTYPE html>
<html lang="en">

<head>
    <title>SeaCloud - Dashboard</title>
    <?php require PROJECT_ROOT . '/includes/head.php'; ?>
</head>

<body>

<?php require PROJECT_ROOT . '/includes/navbar.php'; ?>

<main id="main">

    <?php echo breadcrumb('Dashboard'); ?>

    <!-- ======= Dashboard Section ======= -->
    <section id="dashboard" class="inner-page">
        <div class="container">

            <div class="row gy-4">

                <?php echo accountMenu(); ?>

                <div class="col-md-8 col-lg-9">
                    <a href="/account/new-server.php" class="btn btn-primary float-end">
                        New server
                    </a>

                    <h2 class="mb-3">Servers</h2>

                    <?php
                    if (empty($servers)) {
                    ?>
                    <div class="alert alert-info">
                        <p>
                            No server configured.
                        </p>
                    </div>
                    <?php
                    } else {
                        foreach ($servers as $server) {
                    ?>
                    <div class="card mb-3">
                        <div class="row g-0">
                            <div class="col-md-3">
                                <div class="card-body">
                                    <h4 class="mb-3">
                                        <a href="/account/server-detail.php?id=<?php echo $server->getId(); ?>">
                                            <?php echo $server->getName(); ?>
                                        </a>
                                    </h4>
                                    <p>
                                        <strong>Status</strong>
                                        <?php echo serverStateBadge($server); ?>
                                    </p>
                                    <?php if (!empty($ip = $server->getIp())) { ?>
                                    <p>
                                        <strong>IP</strong>
                                        <span class="text-muted"><?php echo $ip ?></span>
                                    </p>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-md-6 border-start">
                                <div class="card-body">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5">Datacenter</dt>
                                        <dd class="col-sm-7"><?php echo $server->getLocation()->getName(); ?></dd>

                                        <dt class="col-sm-5">Distribution</dt>
                                        <dd class="col-sm-7"><?php echo $server->getDistribution()->getName(); ?></dd>

                                        <dt class="col-sm-5">CPU</dt>
                                        <dd class="col-sm-7"><?php echo $server->getCpu(); ?> Intel CPUs</dd>

                                        <dt class="col-sm-5">RAM</dt>
                                        <dd class="col-sm-7"><?php echo $server->getRam(); ?> GB</dd>
                                    </dl>
                                </div>
                            </div>
                            <div class="col-md-3 border-start">
                                <div class="card-body">
                                    <a class="btn btn-primary mb-3"
                                       href="/account/restart.php?id=<?php echo $server->getId(); ?>">
                                        Restart
                                    </a>
                                    <br>
                                    <a class="btn btn-light"
                                       href="/account/reset.php?id=<?php echo $server->getId(); ?>">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        } // Fin foreach
                    } // Fin if
                    ?>
                </div>
            </div>

        </div>
    </section><!-- End Login Section -->

</main><!-- End #main -->

<?php require PROJECT_ROOT . '/includes/footer.php'; ?>

</body>

</html>
