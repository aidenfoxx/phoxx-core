<?php

namespace Phoxx\Core\Database;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection as DoctrineConnection;
use Doctrine\DBAL\Driver\PDOMySql\Driver as PDOMySQLDriver;

class Connection extends DoctrineConnection
{
    protected $prefix;

    public function __construct(
        string $name,
        string $user = 'root',
        string $password = '',
        string $prefix = 'foxx_',
        string $host = '127.0.0.1',
        int $port = 3306
    ) {
        parent::__construct(
            [
                'dbname' => $name,
                'user' => $user,
                'password' => $password,
                'host' => $host,
                'port' => $port,
            ],
            new PDOMySQLDriver(),
            new Configuration(),
            new EventManager()
        );

        $this->prefix = $prefix;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
