<?php

declare(strict_types=1);

namespace Manager;

use Entity\User;
use PDO;
use Security\EncoderInterface;

class UserManager extends AbstractManager
{
    private EncoderInterface $encoder;


    public function __construct(PDO $connection, EncoderInterface $encoder)
    {
        parent::__construct($connection);

        $this->encoder = $encoder;
    }

    public function persist(User $user)
    {
        if (null === $user->getId()) {
            $this->insert($user);

            return;
        }

        $this->update($user);
    }

    public function remove(User $user)
    {
        $sql = 'DELETE FROM user WHERE id=:id LIMIT 1';

        $delete = $this->connection->prepare($sql);

        $delete->execute([
            'id' => $user->getId(),
        ]);

        $user->setId(null);
    }

    private function insert(User $user)
    {
        $this->encodePassword($user);

        $sql = 'INSERT INTO user(email, password) VALUES (:email, :password)';

        $insert = $this->connection->prepare($sql);

        $insert->execute([
            'email'    => $user->getEmail(),
            'password' => $user->getPassword(),
        ]);

        $user->setId((int)$this->connection->lastInsertId());
    }

    private function update(User $user)
    {
        $this->encodePassword($user);

        $sql = 'UPDATE user SET email=:email, password=:password WHERE id=:id LIMIT 1';

        $update = $this->connection->prepare($sql);

        $update->execute([
            'id'       => $user->getId(),
            'email'    => $user->getEmail(),
            'password' => $user->getPassword(),
        ]);
    }

    private function encodePassword(User $user): void
    {
        if (empty($plain = $user->getPlainPassword())) {
            return;
        }

        $encoded = $this->encoder->encode($plain);

        $user->setPassword($encoded);
    }
}
