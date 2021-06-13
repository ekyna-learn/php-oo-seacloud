<?php

declare(strict_types=1);

namespace Service;

use Entity\Server;
use Repository\ServerRepository;

use function sprintf;
use function str_pad;

use const STR_PAD_LEFT;

/**
 * Class NameGenerator
 * @package Service
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class NameGenerator
{
    private ServerRepository $serverReposiroty;

    public function __construct(ServerRepository $serverReposiroty)
    {
        $this->serverReposiroty = $serverReposiroty;
    }

    public function generate(Server $server): void
    {
        if (!empty($server->getName())) {
            return;
        }

        $base = sprintf(
            'SC-%s-%s-',
            $server->getLocation()->getCode(),
            $server->getDistribution()->getCode()
        );

        $count = 0;

        do {
            $count++;
            $name = $base . str_pad((string)$count, 2, '0', STR_PAD_LEFT);
        } while ($this->serverReposiroty->findOneByUserAndName($server->getUser(), $name));

        $server->setName($name);
    }
}
