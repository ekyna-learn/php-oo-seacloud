<?php

declare(strict_types=1);

require __DIR__ . '/../../bootstrap.php';

/** @var PDO $connection */

use Repository\UserRepository;
use Security\Firewall;

$repository = new UserRepository($connection);
$firewall = new Firewall($repository);
$firewall->denyAccessUnlessAuthenticated();

$user = $firewall->getUser();

use Manager\UserManager;
use Security\ArgonEncoder;

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passwordFirst = $_POST['password-first'] ?? '';
    $passwordSecond = $_POST['password-second'] ?? '';

    if (empty($passwordFirst)) {
        $errors['password-first'] = 'Please enter your password';
    } elseif (empty($passwordSecond)) {
        $errors['password-second'] = 'Please enter your password confirmation';
    } elseif ($passwordFirst !== $passwordSecond) {
        $errors['password-second'] = 'The password and its confirmation must match.';
    } else {
        $user->setPlainPassword($passwordFirst);

        $encoder = new ArgonEncoder();
        $manager = new UserManager($connection, $encoder);
        $manager->persist($user);

        $firewall->logout();

        redirect('/sign-in.php');
    }
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

    <?php echo breadcrumb('Profile'); ?>

    <!-- ======= Dashboard Section ======= -->
    <section id="dashboard" class="inner-page">
        <div class="container">

            <div class="row gy-4">

                <?php echo accountMenu(); ?>

                <div class="col-md-8 col-lg-9">
                    <h2 class="mb-5">My profile</h2>

                    <form action="/account/profile.php" method="post">
                        <div class="mb-4">
                            <label for="profile-email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="profile-email" value="<?php echo $user->getEmail(); ?>" readonly>
                        </div>

                        <div class="row mb-4">
                            <div class="col">
                                <label for="profile-password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="profile-password" name="password-first" required>
                                <?php echo formError($errors, 'password-first'); ?>
                            </div>

                            <div class="col">
                                <label for="profile-confirmation" class="form-label">Confirmation</label>
                                <input type="password" class="form-control" id="profile-confirmation" name="password-second" required>
                                <?php echo formError($errors, 'password-second'); ?>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </section><!-- End Login Section -->

</main><!-- End #main -->


<?php require PROJECT_ROOT . '/includes/footer.php'; ?>

</body>

</html>
