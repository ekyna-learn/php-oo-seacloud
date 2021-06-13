<?php

declare(strict_types=1);

namespace Manager;

use Entity\Distribution;

class DistributionManager extends AbstractManager
{
    public function persist(Distribution $distribution)
    {
        if (null === $distribution->getId()) {
            $this->insert($distribution);

            return;
        }

        $this->update($distribution);
    }

    public function remove(Distribution $distribution)
    {
        $sql = 'DELETE FROM distribution WHERE id=:id LIMIT 1';

        $delete = $this->connection->prepare($sql);

        $delete->execute([
            'id' => $distribution->getId(),
        ]);

        $distribution->setId(null);
    }

    private function insert(Distribution $distribution)
    {
        $sql = 'INSERT INTO distribution(name, code) VALUES (:name, :code)';

        $insert = $this->connection->prepare($sql);

        $insert->execute([
            'name' => $distribution->getName(),
            'code' => $distribution->getCode(),
        ]);

        $distribution->setId((int)$this->connection->lastInsertId());
    }

    private function update(Distribution $distribution)
    {
        $sql = 'UPDATE distribution SET name=:name, code=:code WHERE id=:id LIMIT 1';

        $update = $this->connection->prepare($sql);

        $update->execute([
            'id'   => $distribution->getId(),
            'name' => $distribution->getName(),
            'code' => $distribution->getCode(),
        ]);
    }
}
