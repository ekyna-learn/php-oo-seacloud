<?php

declare(strict_types=1);

namespace Repository;

use Entity\Distribution;

class DistributionRepository extends AbstractRepository
{
    /**
     * Récupère toutes les distributions.
     *
     * @return Distribution[]
     */
    public function findAll(): array
    {
        $sql = 'SELECT id, name, code FROM distribution';

        $statement = $this->connection->query($sql);

        $distributions = [];

        while (false !== $data = $statement->fetch()) {
            $distributions[] = $this->hydrate($data);
        }

        return $distributions;
    }

    /**
     * Récupère une distribution par son identifiant.
     */
    public function findOneById(int $id): ?Distribution
    {
        $sql = 'SELECT id, name, code FROM distribution WHERE id=:id LIMIT 1';

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
     * Converti les données d'une ligne de résultat de la BDD en objet PHP Distribution.
     */
    private function hydrate(array $data): Distribution
    {
        $distribution = new Distribution();
        $distribution
            ->setId((int)$data['id'])
            ->setName($data['name'])
            ->setCode($data['code']);

        return $distribution;
    }
}
