<?php

namespace Repository;

use PDO;

abstract class AbstractRepository
{
    protected PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }
}
