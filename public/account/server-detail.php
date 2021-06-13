<?php

declare(strict_types=1);

require __DIR__ . '/../../bootstrap.php';

/** @var PDO $connection */

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

?><!DOCTYPE html>
<html lang="en">

<head>
    <title>SeaCloud - Server detail</title>
    <?php require PROJECT_ROOT . '/includes/head.php'; ?>
</head>

<body>

<?php require PROJECT_ROOT . '/includes/navbar.php'; ?>

<main id="main">

    <?php echo breadcrumb('Server detail'); ?>

    <!-- ======= Dashboard Section ======= -->
    <section id="dashboard" class="inner-page">
        <div class="container">

            <div class="row gy-4">

                <?php echo accountMenu(); ?>

                <div class="col-md-8 col-lg-9">
                    <h2 class="mb-5">Server detail</h2>

                    <div class="card">
                        <div class="card-header">
                            <h1 class="h4 my-2">
                                <?php echo $server->getName() ?>
                            </h1>
                        </div>
                        <div class="card-body row">
                            <div class="col">
                                <strong>IP</strong>
                                <span class="text-muted">
                                <?php echo ($ip = $server->getIp()) ? $ip : '(no yet ready)'; ?>
                                </span>
                            </div>
                            <div class="col">
                                <strong>Status</strong>
                                <?php echo serverStateBadge($server); ?>
                            </div>
                        </div>
                        <div class="card-body border-top">
                            <dl class="row mb-0">
                                <dt class="col-sm-5">Datacenter</dt>
                                <dd class="col-sm-7">
                                    <?php echo $server->getLocation()->getName(); ?>
                                </dd>

                                <dt class="col-sm-5">Distribution</dt>
                                <dd class="col-sm-7">
                                    <?php echo $server->getDistribution()->getName(); ?>
                                </dd>

                                <dt class="col-sm-5">CPU</dt>
                                <dd class="col-sm-7"><?php echo $server->getCpu(); ?> Intel CPUs</dd>

                                <dt class="col-sm-5">RAM</dt>
                                <dd class="col-sm-7"><?php echo $server->getRam(); ?> GB</dd>
                            </dl>
                        </div>
                        <div class="card-body border-top">
                            <a class="btn btn-primary me-3"
                               href="/account/restart.php?id=<?php echo $server->getId(); ?>">
                                Restart
                            </a>
                            <a class="btn btn-light"
                               href="/account/reset.php?id=<?php echo $server->getId(); ?>">
                                Reset
                            </a>
                        </div>
                        <div class="card-body border-top">
                            <form class="row row-cols-lg-auto g-3 align-items-center"
                                  action="/account/delete.php?id=<?php echo $server->getId(); ?>"
                                  method="post">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="delete-confirm"
                                               name="confirm" value="1" required>
                                        <label class="form-check-label" for="delete-confirm">
                                            Confirm server deletion
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section><!-- End Login Section -->

</main><!-- End #main -->


<?php require PROJECT_ROOT . '/includes/footer.php'; ?>

</body>

</html>
