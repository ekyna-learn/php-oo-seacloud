<?php

declare(strict_types=1);

namespace Repository;

use Entity\Server;
use Entity\User;
use PDO;

class ServerRepository extends AbstractRepository
{
    private UserRepository         $userRepository;
    private DataCenterRepository   $dataCenterRepository;
    private DistributionRepository $distributionRepository;


    public function __construct(
        PDO $connection,
        UserRepository $userRepository,
        DataCenterRepository $dataCenterRepository,
        DistributionRepository $distributionRepository
    ) {
        parent::__construct($connection);

        $this->userRepository = $userRepository;
        $this->dataCenterRepository = $dataCenterRepository;
        $this->distributionRepository = $distributionRepository;
    }

    /**
     * Récupère un serveur par son utilisateur et son identifiant.
     */
    public function findOneByUserAndId(User $user, int $id): ?Server
    {
        $sql = <<<SQL
            SELECT id, user_id, location_id, distribution_id, name, ip, state, cpu, ram 
            FROM server 
            WHERE user_id=:userId AND id=:id 
            LIMIT 1
            SQL;

        $statement = $this->connection->prepare($sql);

        $statement->execute([
            'userId' => $user->getId(),
            'id'     => $id,
        ]);

        if (false !== $data = $statement->fetch()) {
            return $this->hydrate($data);
        }

        return null;
    }

    /**
     * Récupère les serveurs de l'utilisateur.
     *
     * @return Server[]
     */
    public function findByUser(User $user): array
    {
        $sql = <<<SQL
            SELECT id, user_id, location_id, distribution_id, name, ip, state, cpu, ram 
            FROM server 
            WHERE user_id=:userId
            SQL;

        $statement = $this->connection->prepare($sql);

        $statement->execute([
            'userId' => $user->getId(),
        ]);

        $servers = [];

        while (false !== $data = $statement->fetch()) {
            $servers[] = $this->hydrate($data);
        }

        return $servers;
    }

    /**
     * Récupère un serveur par son utilisateur et son nom.
     */
    public function findOneByUserAndName(User $user, string $name): ?Server
    {
        $sql = <<<SQL
            SELECT id, user_id, location_id, distribution_id, name, ip, state, cpu, ram 
            FROM server 
            WHERE user_id=:userId AND name=:name 
            LIMIT 1
            SQL;

        $statement = $this->connection->prepare($sql);

        $statement->execute([
            'userId' => $user->getId(),
            'name'   => $name,
        ]);

        if (false !== $data = $statement->fetch()) {
            return $this->hydrate($data);
        }

        return null;
    }

    /**
     * Converti les données d'une ligne de résultat de la BDD en objet PHP Server.
     */
    private function hydrate(array $data): Server
    {
        $user = $this->userRepository->findOneById((int)$data['user_id']);
        $dataCenter = $this->dataCenterRepository->findOneById((int)$data['location_id']);
        $distribution = $this->distributionRepository->findOneById((int)$data['distribution_id']);

        $server = new Server();
        $server
            ->setId((int)$data['id'])
            ->setUser($user)
            ->setLocation($dataCenter)
            ->setDistribution($distribution)
            ->setName($data['name'])
            ->setIp($data['ip'])
            ->setState((int)$data['state'])
            ->setCpu((int)$data['cpu'])
            ->setRam((int)$data['ram']);

        return $server;
    }
}
