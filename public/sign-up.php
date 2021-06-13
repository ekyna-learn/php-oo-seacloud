<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

/** @var PDO $connection */

use Entity\User;
use Manager\UserManager;
use Security\ArgonEncoder;

$user = new User();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $passwordFirst = $_POST['password-first'] ?? '';
    $passwordSecond = $_POST['password-second'] ?? '';

    $user->setEmail($email);

    if (empty($email)) {
        $errors['email'] = 'Please enter your email address';
    } elseif (empty($passwordFirst)) {
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

        redirect('/sign-in.php');
    }
}

?><!DOCTYPE html>
<html lang="en">

<head>
    <title>SeaCloud - Signup</title>
    <?php require PROJECT_ROOT . '/includes/head.php'; ?>
</head>

<body>

<?php require PROJECT_ROOT . '/includes/navbar.php'; ?>

<main id="main">

    <?php echo breadcrumb('Sign up'); ?>

    <!-- ======= Signup Section ======= -->
    <section id="signup" class="inner-page">
        <div class="container">

            <div class="section-title" data-aos="fade-up">
                <h2>Sign up</h2>
            </div>

            <div class="p-5 rounded bg-light" style="max-width: 640px;margin:auto" data-aos="fade-up">
                <h1 class="h2 mb-3 text-center">Create your Sea Cloud account</h1>
                <p class="lead border-bottom pb-3 mb-3 text-center">
                    Create your account and start working with your servers.
                </p>

                <form action="/sign-up.php" method="post" novalidate>
                    <div class="mb-3">
                        <label for="signup-email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="signup-email" placeholder="name@example.com"
                               name="email" value="<?php echo $user->getEmail(); ?>" required>
                        <?php echo formError($errors, 'email'); ?>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label for="signup-password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="signup-password" name="password-first" required>
                            <?php echo formError($errors, 'password-first'); ?>
                        </div>

                        <div class="col">
                            <label for="signup-confirmation" class="form-label">Confirmation</label>
                            <input type="password" class="form-control" id="signup-confirmation" name="password-second" required>
                            <?php echo formError($errors, 'password-second'); ?>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">Sign up</button>
                    </div>
                </form>
            </div>
        </div>
    </section><!-- End Signup Section -->

</main><!-- End #main -->

<?php require PROJECT_ROOT . '/includes/footer.php'; ?>

</body>

</html>
