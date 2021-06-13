<?php

declare(strict_types=1);

require __DIR__ . '/../../bootstrap.php';

/** @var PDO $connection */

use Entity\Server;
use Manager\ServerManager;
use Repository\DataCenterRepository;
use Repository\DistributionRepository;
use Repository\ServerRepository;
use Repository\UserRepository;
use Security\Firewall;
use Service\NameGenerator;

$userRepository = new UserRepository($connection);
$firewall = new Firewall($userRepository);
$firewall->denyAccessUnlessAuthenticated();

$dataCenterRepository = new DataCenterRepository($connection);
$dataCenters = $dataCenterRepository->findAll();

$distributionRepository = new DistributionRepository($connection);
$distributions = $distributionRepository->findAll();

$server = new Server();
$server
    ->setUser($firewall->getUser())
    ->setLocation(current($dataCenters))
    ->setDistribution(current($distributions));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dataCenter = $dataCenterRepository->findOneById((int) $_POST['data-center']);
    $distribution = $distributionRepository->findOneById((int) $_POST['distribution']);

    $server
        ->setName($_POST['name'])
        ->setLocation($dataCenter)
        ->setDistribution($distribution)
        ->setCpu((int) $_POST['cpu'])
        ->setRam((int) $_POST['ram']);

    $serverRepository = new ServerRepository(
        $connection,
        $userRepository,
        $dataCenterRepository,
        $distributionRepository
    );

    $generator = new NameGenerator($serverRepository);
    $generator->generate($server);

    $serverManager = new ServerManager($connection);
    $serverManager->persist($server);

    redirect('/account/dashboard.php');
}

?><!DOCTYPE html>
<html lang="en">

<head>
    <title>SeaCloud - Create server</title>
    <?php require PROJECT_ROOT . '/includes/head.php'; ?>
</head>

<body>

<?php require PROJECT_ROOT . '/includes/navbar.php'; ?>

<main id="main">

    <?php echo breadcrumb('New server'); ?>

    <!-- ======= Dashboard Section ======= -->
    <section id="dashboard" class="inner-page">
        <div class="container">

            <div class="row gy-4">

                <?php echo accountMenu(); ?>

                <div class="col-md-8 col-lg-9">
                    <h2 class="mb-5">Create your new server</h2>

                    <form action="/account/new-server.php" method="post">
                        <div class="mb-4">
                            <label for="new-server-name" class="form-label">Server name</label>
                            <input type="email" class="form-control" id="new-server-name"
                                   name="name" value="<?php echo $server->getName(); ?>">
                            <div id="new-server-name-help" class="form-text">Leave blank for auto generation.</div>
                        </div>

                        <div class="mb-4">
                            <label for="new-server-datacenter" class="form-label">Datacenter</label>
                            <select class="form-select" id="new-server-datacenter" name="data-center">
                                <?php
                                foreach ($dataCenters as $dataCenter) {
                                    $selected = $dataCenter->getId() === $server->getLocation()->getId() ? 'selected' : '';
                                    echo '<option value="' . $dataCenter->getId() . '" ' . $selected . '>' . $dataCenter->getName() . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="new-server-distribution" class="form-label">Distribution</label>
                            <select class="form-select" id="new-server-distribution" name="distribution">
                                <?php
                                foreach ($distributions as $distribution) {
                                    $selected = $distribution->getId() === $server->getDistribution()->getId() ? 'selected' : '';
                                    echo '<option value="' . $distribution->getId() . '" ' . $selected . '>' . $distribution->getName() . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-4 slider">
                            <span class="badge badge bg-primary float-end"><span>1</span> Intel CPU</span>
                            <label for="new-server-cpu" class="form-label">CPU</label>
                            <input type="range" class="form-range" min="1" max="16" id="new-server-cpu"
                                   name="cpu" value="<?php echo $server->getCpu(); ?>">
                        </div>

                        <div class="mb-4 slider">
                            <span class="badge badge bg-primary float-end"><span>1</span> GB RAM</span>
                            <label for="new-server-ram" class="form-label">RAM</label>
                            <input type="range" class="form-range" min="1" max="16" id="new-server-ram"
                                   name="ram" value="<?php echo $server->getRam(); ?>">
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </section><!-- End Login Section -->

</main><!-- End #main -->


<?php require PROJECT_ROOT . '/includes/footer.php'; ?>

<script src="/js/create.js"></script>

</body>

</html>
