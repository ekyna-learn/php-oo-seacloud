<?php

declare(strict_types=1);

namespace Repository;

use Entity\DataCenter;

class DataCenterRepository extends AbstractRepository
{
    /**
     * Récupère tous les datacenters.
     *
     * @return DataCenter[]
     */
    public function findAll(): array
    {
        $sql = 'SELECT id, name, code FROM data_center';

        $statement = $this->connection->query($sql);

        $dataCenters = [];

        while (false !== $data = $statement->fetch()) {
            $dataCenters[] = $this->hydrate($data);
        }

        return $dataCenters;
    }

    /**
     * Récupère un datacenter par son identifiant.
     */
    public function findOneById(int $id): ?DataCenter
    {
        $sql = 'SELECT id, name, code FROM data_center WHERE id=:id LIMIT 1';

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
     * Converti les données d'une ligne de résultat de la BDD en objet PHP DataCenter.
     */
    private function hydrate(array $data): DataCenter
    {
        $dataCenter = new DataCenter();
        $dataCenter
            ->setId((int)$data['id'])
            ->setName($data['name'])
            ->setCode($data['code']);

        return $dataCenter;
    }
}
