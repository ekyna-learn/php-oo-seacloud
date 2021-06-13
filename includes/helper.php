<?php

declare(strict_types=1);

use Entity\Server;

function breadcrumb(string $page): string
{
    return <<<HTML
        <!-- ======= Breadcrumbs Section ======= -->
        <section class="breadcrumbs">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>$page</h2>
                    <ol>
                        <li><a href="/">Home</a></li>
                        <li>$page</li>
                    </ol>
                </div>
            </div>
        </section><!-- End Breadcrumbs Section -->
        HTML;
}

function formError(array $errors, string $field): string
{
    if (!isset($errors[$field])) {
        return '';
    }

    return <<<HTML
        <p style="color:red">
            $errors[$field]
        </p>
        HTML;
}

function redirect(string $url): void
{
    http_response_code(302);
    header('Location: ' . $url);
    exit;
}

function accountMenu(): string
{
    return <<<HTML
    <div class="col-md-4 col-lg-3">
        <div class="list-group pe-5">
            <a href="/account/dashboard.php" class="list-group-item list-group-item-action">
                Dashboard
            </a>
            <a href="/account/profile.php" class="list-group-item list-group-item-action">
                My profile
            </a>
            <a href="/logout.php" class="list-group-item list-group-item-action">
                Logout
            </a>
        </div>
    </div>
    HTML;
}

function serverStateBadge(Server $server): string
{
    $theme = 'warning';
    $label = 'Pending';

    if ($server->getState() === Server::STATE_READY) {
        $theme = 'success';
        $label = 'Ready';
    } elseif ($server->getState() === Server::STATE_STOPPED) {
        $theme = 'danger';
        $label = 'Stopped';
    }

    return sprintf('<span class="badge bg-%s">%s</span>', $theme, $label);
}
