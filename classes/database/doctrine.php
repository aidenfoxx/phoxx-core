<?php

namespace Phoxx\Core\Database;

use Memcached;
use Redis;

use Doctrine\ORM\Events;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\Common\EventManager;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\Common\Cache\RedisCache;

use Phoxx\Core\Utilities\Config;
use Phoxx\Core\Database\Doctrine\TablePrefix;
use Phoxx\Core\Database\Doctrine\ModelDate;

class Doctrine
{
	const CACHE_FILE = 'file';
	const CACHE_APCU = 'apcu';
	const CACHE_MEMCACHED = 'memcached';
	const CACHE_REDIS = 'redis';

	protected static $instance;

	public static function core(): self
	{
		if (isset(static::$instance) === false) {
			$config = Config::core()->getFile('database');

			static::$instance = new static(
				$config->DATABASE_NAME,
				$config->DATABASE_USER,
				$config->DATABASE_PASSWORD,
				$config->DATABASE_PREFIX,
				$config->DATABASE_HOST,
				$config->DATABASE_PORT,
				$config->DATABASE_CACHE,
				$config->DATABASE_CACHE_METHOD
			);
			static::$instance->addPath(PATH_CORE.'/doctrine');	
		}
		return static::$instance;
	}

	private $connection;

	private $entityManager;

	protected $paths = array();

	public function __construct(
		string $name,
		string $user = 'root',
		string $password = '',
		string $prefix = 'foxx_',
		string $host = '127.0.0.1', 
		int $port = 3306,
		bool $cache = false,
		string $cacheMethod = 'default'
	) {
		$cache = new ArrayCache();

		if ($cache === true) {
			switch ($cacheMethod) {
				case self::CACHE_FILE:
					$cache = new FilesystemCache(PATH_CACHE.'/doctrine');
					break;

				case self::CACHE_APCU:
					$cache = new ApcuCache();
					break;

				case self::CACHE_MEMCACHED:
					$memcached = new Memcached();
					$memcached->addServers((array)Config::core()->getFile('cache/memcached')->MEMCACHED_SERVERS);
					$cache = new MemcachedCache();
					$cache->setMemcache($memcached);
					break;

				case self::CACHE_REDIS:
					$redis = new Redis();
					$redis->connect(
						(string)Config::core()->getFile('cache/redis')->REDIS_SERVER,
						(int)Config::core()->getFile('cache/redis')->REDIS_PORT
					);
					$cache = new RedisCache();
					$cache->setRedis($redis);
					break;
			}
		}

		$eventManager = new EventManager();
		$eventManager->addEventListener(Events::loadClassMetadata, new TablePrefix($prefix));
		$eventManager->addEventListener(Events::prePersist, new ModelDate($prefix));
		$eventManager->addEventListener(Events::preUpdate, new ModelDate($prefix));

		$config = new Configuration();
		$config->setQueryCacheImpl($cache);
		$config->setResultCacheImpl($cache);
		$config->setMetadataCacheImpl($cache);
		$config->setMetadataDriverImpl(new XmlDriver($this->paths));
		$config->setProxyDir(PATH_CACHE.'/doctrine/proxy');
		$config->setProxyNamespace('DoctrineProxy');
		$config->setAutoGenerateProxyClasses(true);

		$this->connection = DriverManager::getConnection(array(
			'dbname' => $name,
			'user' => $user,
			'password' => $password,
			'host' => $host,
			'port' => $port,
			'driver' => 'pdo_mysql',
		), $config, $eventManager);

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