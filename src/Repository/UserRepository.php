<?php

declare(strict_types=1);

namespace Repository;

use Entity\User;

class UserRepository extends AbstractRepository
{
    /**
     * Récupère un utilisateur par son identifiant.
     */
    public function findOneById(int $id): ?User
    {
        $sql = 'SELECT id, email, password FROM user WHERE id=:id LIMIT 1';

        $statement = $this->connection->prepare($sql);

        $statement->execute([
            'id' => $id,
        ]);

        if (false !== $data = $statement->fetch()) {
            return $this->hydrate($data);
        }

        return null;
    }

    /**
     * Récupère un utilisateur par son email.
     */
    public function findOneByEmail(string $email): ?User
    {
        $sql = 'SELECT id, email, password FROM user WHERE email=:email LIMIT 1';

        $statement = $this->connection->prepare($sql);

        $statement->execute([
            'email' => $email,
        ]);

        if (false !== $data = $statement->fetch()) {
            return $this->hydrate($data);
        }

        return null;
    }

    /**
     * Converti les données d'une ligne de résultat de la BDD en objet PHP User.
     */
    private function hydrate(array $data): User
    {
        $user = new User();
        $user
            ->setId((int)$data['id'])
            ->setEmail($data['email'])
            ->setPassword($data['password']);

        return $user;
    }
}
