<?php

namespace Manager;

use PDO;

abstract class AbstractManager
{
    protected PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }
}
