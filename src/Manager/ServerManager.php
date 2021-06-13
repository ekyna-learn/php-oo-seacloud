<?php

declare(strict_types=1);

namespace Manager;

use Entity\Server;

class ServerManager extends AbstractManager
{
    public function persist(Server $server)
    {
        if (null === $server->getId()) {
            $this->insert($server);

            return;
        }

        $this->update($server);
    }

    public function remove(Server $server)
    {
        $sql = 'DELETE FROM `server` WHERE id=:id LIMIT 1';

        $delete = $this->connection->prepare($sql);

        $delete->execute([
            'id' => $server->getId(),
        ]);

        $server->setId(null);
    }

    private function insert(Server $server)
    {
        $sql = <<<SQL
            INSERT INTO `server`(user_id, location_id, distribution_id, name, ip, state, cpu, ram)
            VALUES (:user, :location, :distribution, :name, :ip, :state, :cpu, :ram)
            SQL;

        $insert = $this->connection->prepare($sql);

        $insert->execute([
            'user'         => $server->getUser()->getId(),
            'location'     => $server->getLocation()->getId(),
            'distribution' => $server->getDistribution()->getId(),
            'name'         => $server->getName(),
            'ip'           => $server->getIp(),
            'state'        => $server->getState(),
            'cpu'          => $server->getCpu(),
            'ram'          => $server->getRam(),
        ]);

        $server->setId((int)$this->connection->lastInsertId());
    }

    private function update(Server $server)
    {
        $sql = <<<SQL
            UPDATE `server` SET 
                user_id=:user, 
                location_id=:location, 
                distribution_id=:distribution, 
                name=:name,
                ip=:ip,
                state=:state,
                cpu=:cpu,
                ram=:ram
            WHERE id=:id LIMIT 1
            SQL;

        $update = $this->connection->prepare($sql);

        $update->execute([
            'id'           => $server->getId(),
            'user'         => $server->getUser()->getId(),
            'location'     => $server->getLocation()->getId(),
            'distribution' => $server->getDistribution()->getId(),
            'name'         => $server->getName(),
            'ip'           => $server->getIp(),
            'state'        => $server->getState(),
            'cpu'          => $server->getCpu(),
            'ram'          => $server->getRam(),
        ]);
    }
}
