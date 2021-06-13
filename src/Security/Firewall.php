<?php

declare(strict_types=1);

namespace Security;

use Entity\User;
use Repository\UserRepository;

use function header;
use function http_response_code;
use function password_verify;

/**
 * Class Firewall
 * @package Security
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Firewall
{
    private const SESSION_KEY = 'user_id';

    private UserRepository $repository;

    private ?User   $user        = null; // L'utilisateur, si connecté
    private ?string $error       = null; // Erreur d'authentification
    private bool    $initialized = false; // Marqueur d'initialisation lorsque le pare-feu lit la session


    /**
     * Constructor.
     *
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Authentifie l'utilisateur d'après l'adresse email et le mot de passe saisi.
     *
     * @param string $email
     * @param string $plainPassword
     *
     * @return bool
     */
    public function authenticate(string $email, string $plainPassword): bool
    {
        // Récupère l'utilisateur par son adresse email
        $user = $this->repository->findOneByEmail($email);
        if (!$user) {
            $this->error = 'Unknown email address.';

            return false;
        }

        // Vérifie que le mot de passe saisi correspond au mot de passe (crypté) enregistré dans la BDD
        if (!password_verify($plainPassword, $user->getPassword())) {
            $this->error = 'Invalid password.';

            return false;
        }

        // Marque le pare-feu comme initialisé (pour que la méthode initialize() ne remplace pas cet utilisateur)
        $this->initialized = true;

        // Mémorise l'utilisateur authentifié dans la session
        $_SESSION[self::SESSION_KEY] = $user->getId();

        // Mémorise l'utilisateur authentifié dans la propriété privée user
        $this->user = $user;

        return true;
    }

    /**
     * Renvoie la dernière erreur d'authentification.
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Renvoie l'utilisateur authentifié.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        $this->initialize();

        return $this->user;
    }

    /**
     * Empêche l'accès si non authentifié.
     */
    public function denyAccessUnlessAuthenticated(): void
    {
        if ($this->getUser()) {
            // L'utilisateur est authentifié : accès autorisé
            return;
        }

        // Accès non autorisé
        http_response_code(403);
        header('Location: /sign-in.php');
        exit;
    }

    public function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
    }

    /**
     * Initialise le pare-feu, en récupérant l'utilisateur mémorisé dans la session.
     */
    private function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->initialized = true;

        if (!isset($_SESSION[self::SESSION_KEY])) {
            return;
        }

        $userId = (int)$_SESSION[self::SESSION_KEY];

        $this->user = $this->repository->findOneById($userId);
    }
}
