<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

/** @var PDO $connection */

use Repository\UserRepository;
use Security\Firewall;

$repository = new UserRepository($connection);
$firewall = new Firewall($repository);

if ($firewall->getUser()) {
    redirect('/account/dashboard.php');
}

$email = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email)) {
        $errors['email'] = 'Please enter your email address';
    } elseif(empty($password)) {
        $errors['password'] = 'Please enter your password';
    } else {

        if ($firewall->authenticate($email, $password)) {
            redirect('/account/dashboard.php');
        }

        $errors['global'] = $firewall->getError();
    }
}

?><!DOCTYPE html>
<html lang="en">

<head>
    <title>SeaCloud - Sign in</title>
    <?php require PROJECT_ROOT . '/includes/head.php'; ?>
</head>

<body>

<?php require PROJECT_ROOT . '/includes/navbar.php'; ?>

<main id="main">

    <?php echo breadcrumb('Sign in'); ?>

    <!-- ======= Login Section ======= -->
    <section id="login" class="inner-page">
        <div class="container">

            <div class="section-title" data-aos="fade-up">
                <h2>Sign in</h2>
            </div>

            <div class="p-5 rounded bg-light" style="max-width: 640px;margin:auto" data-aos="fade-up">
                <h1 class="h2 mb-3 text-center">Sea Cloud account</h1>
                <p class="lead border-bottom pb-3 mb-3 text-center">
                    Sign in to your account and start working with your servers.
                </p>

                <form action="/sign-in.php" method="post" novalidate>

                    <?php echo formError($errors, 'global'); ?>

                    <div class="mb-3">
                        <label for="login-email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="login-email" placeholder="name@example.com"
                               name="email" value="<?php echo $email; ?>" required>
                        <?php echo formError($errors, 'email'); ?>
                    </div>

                    <div class="mb-3">
                        <label for="login-password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="login-password"
                               name="password" required>
                        <?php echo formError($errors, 'password'); ?>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">Sign in</button>
                    </div>
                </form>
            </div>
        </div>
    </section><!-- End Login Section -->

</main><!-- End #main -->

<?php require PROJECT_ROOT . '/includes/footer.php'; ?>

</body>

</html>
