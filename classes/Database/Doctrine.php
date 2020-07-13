<?php

namespace Phoxx\Core\Database;

use Doctrine\ORM\Events;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\DBAL\DriverManager;
use Doctrine\Common\EventManager;

use Phoxx\Core\Cache\Cache;
use Phoxx\Core\Cache\Drivers\ArrayDriver;
use Phoxx\Core\Database\Doctrine\CacheInterface;
use Phoxx\Core\Database\Doctrine\Events\TablePrefix;
use Phoxx\Core\Database\Doctrine\Events\ModelDate;

class Doctrine
{
  private $connection;

  private $entityManager;

  protected $paths = [];

  public function __construct(
    string $name,
    string $user = 'root',
    string $password = '',
    string $prefix = 'foxx_',
    string $host = '127.0.0.1',
    int $port = 3306,
    ?Cache $cache = null
  ) {
    $eventManager = new EventManager();
    $eventManager->addEventListener(Events::loadClassMetadata, new TablePrefix($prefix));
    $eventManager->addEventListener(Events::prePersist, new ModelDate($prefix));
    $eventManager->addEventListener(Events::preUpdate, new ModelDate($prefix));

    $config = new Configuration();
    $config->setMetadataDriverImpl(new XmlDriver($this->paths));
    $config->setProxyDir(PATH_CACHE . '/doctrine/proxy');
    $config->setProxyNamespace('DoctrineProxy');
    $config->setAutoGenerateProxyClasses(true);

    if ($cache !== null) {
      $doctrineCache = new CacheInterface($cache);

      $config->setQueryCacheImpl($doctrineCache);
      $config->setResultCacheImpl($doctrineCache);
      $config->setMetadataCacheImpl($doctrineCache);
    }

    /**
     * TODO: Maybe pass in the connection as arg (maybe no need for $config and $cache)?
     */
    $this->connection = DriverManager::getConnection([
      'dbname' => $name,
      'user' => $user,
      'password' => $password,
      'host' => $host,
      'port' => $port,
      'driver' => 'pdo_mysql',
    ], $config, $eventManager);

    $this->entityManager = EntityManager::create($this->connection, $config, $eventManager);
  }

  public function getConnection(): Connection
  {
    return $this->connection;
  }

  public function getEntityManager(): EntityManager
  {
    return $this->entityManager;
  }

  public function addPath(string $path): void
  {
    if (isset($this->paths[$path]) === false) {
      $this->paths[$path] = true;
      $this->entityManager->getConfiguration()->setMetadataDriverImpl(new XmlDriver(array_keys($this->paths)));
    }
  }
}
