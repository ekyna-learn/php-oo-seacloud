<?php

declare(strict_types=1);

namespace Manager;

use Entity\DataCenter;

class DataCenterManager extends AbstractManager
{
    public function persist(DataCenter $dataCenter)
    {
        if (null === $dataCenter->getId()) {
            $this->insert($dataCenter);

            return;
        }

        $this->update($dataCenter);
    }

    public function remove(DataCenter $dataCenter)
    {
        $sql = 'DELETE FROM data_center WHERE id=:id LIMIT 1';

        $delete = $this->connection->prepare($sql);

        $delete->execute([
            'id' => $dataCenter->getId(),
        ]);

        $dataCenter->setId(null);
    }

    private function insert(DataCenter $dataCenter)
    {
        $sql = 'INSERT INTO data_center(name, code) VALUES (:name, :code)';

        $insert = $this->connection->prepare($sql);

        $insert->execute([
            'name' => $dataCenter->getName(),
            'code' => $dataCenter->getCode(),
        ]);

        $dataCenter->setId((int)$this->connection->lastInsertId());
    }

    private function update(DataCenter $dataCenter)
    {
        $sql = 'UPDATE data_center SET name=:name, code=:code WHERE id=:id LIMIT 1';

        $update = $this->connection->prepare($sql);

        $update->execute([
            'id'   => $dataCenter->getId(),
            'name' => $dataCenter->getName(),
            'code' => $dataCenter->getCode(),
        ]);
    }
}
