<?php

namespace Phoxx\Core\Database;

use Doctrine\Common\EventManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Driver\XmlDriver;

use Phoxx\Core\Cache\Cache;
use Phoxx\Core\Database\Doctrine\CacheInterface;
use Phoxx\Core\Database\Doctrine\Events\TablePrefix;
use Phoxx\Core\Database\Doctrine\Events\ModelDate;

class EntityManager extends DoctrineEntityManager
{
  protected $paths = [];

  public function __construct(Connection $connection, ?Cache $cache = null)
  {
    $tablePrefix = $connection->getPrefix();

    $eventManager = new EventManager();
    $eventManager->addEventListener(Events::loadClassMetadata, new TablePrefix($tablePrefix));
    $eventManager->addEventListener(Events::prePersist, new ModelDate());
    $eventManager->addEventListener(Events::preUpdate, new ModelDate());

    $config = new Configuration();
    $config->setMetadataDriverImpl(new XmlDriver(null));
    $config->setProxyDir(PATH_CACHE . '/doctrine/proxy');
    $config->setProxyNamespace('DoctrineProxy');
    $config->setAutoGenerateProxyClasses(true);

    if ($cache !== null) {
      $doctrineCache = new CacheInterface($cache);

      $config->setQueryCacheImpl($doctrineCache);
      $config->setResultCacheImpl($doctrineCache);
      $config->setMetadataCacheImpl($doctrineCache);
    }

    parent::__construct($connection, $config, $eventManager);
  }

  public function addPath(string $path): void
  {
    if (isset($this->paths[$path]) === false) {
      $this->paths[$path] = true;
      $this->getConfiguration()->setMetadataDriverImpl(new XmlDriver(array_keys($this->paths)));
    }
  }
}
