<?php

use Whoops\Run as Whoops;
use Whoops\Handler\PrettyPageHandler;

use Phoxx\Core\Cache\Cache;
use Phoxx\Core\Cache\Drivers\ApcuDriver;
use Phoxx\Core\Cache\Drivers\ArrayDriver;
use Phoxx\Core\Cache\Drivers\FileDriver;
use Phoxx\Core\Cache\Drivers\MemcachedDriver;
use Phoxx\Core\Cache\Drivers\RedisDriver;
use Phoxx\Core\Database\Connection;
use Phoxx\Core\Database\EntityManager;
use Phoxx\Core\File\FileManager;
use Phoxx\Core\File\ImageManager;
use Phoxx\Core\Framework\ServiceContainer;
use Phoxx\Core\Mailer\Drivers\MailDriver;
use Phoxx\Core\Mailer\Mailer;
use Phoxx\Core\Renderer\Drivers\PhpDriver;
use Phoxx\Core\Renderer\Drivers\SmartyDriver;
use Phoxx\Core\Renderer\Drivers\TwigDriver;
use Phoxx\Core\Renderer\Renderer;
use Phoxx\Core\Router\RouteContainer;
use Phoxx\Core\Session\Drivers\CacheDriver;
use Phoxx\Core\Session\Drivers\NativeDriver;
use Phoxx\Core\Session\Session;
use Phoxx\Core\Utilities\Config;

if (!function_exists('register_bootstrap')) {
  return;
}

function generate_cache(): Cache
{
  $config = new Config();
  $config->addPath(PATH_BASE . '/config');

  switch ((string)$config->open('core')->CORE_CACHE) {
    case 'apcu':
      $driver = new ApcuDriver();
      break;

    case 'memcached':
      $driver = new MemcachedDriver();
      foreach ((array)$config->open('cache/memcached')->MEMCACHED_HOSTS as $host) {
        $driver->addServer((string)$host[0], (int)$host[1], (int)$host[2]);
      }
      break;

    case 'redis':
      $driver = new RedisDriver((string)$config->open('cache/redis')->REDIS_HOST, (int)$redis->REDIS_PORT);
      break;

    case 'file':
      $driver = new FileDriver(PATH_CACHE . '/core');
      break;

    default:
      $driver = new ArrayDriver();
      break;
  }

  $cache = new Cache($driver);

  return $cache;
}

function generate_config(Cache $cache): Config
{
  $config = new Config($cache);
  $config->addPath(PATH_BASE . '/config');

  return $config;
}

function generate_connection(Config $config): Connection
{
  $database = $config->open('database');

  return new Connection(
    (string)$database->DATABASE_NAME,
    (string)$database->DATABASE_USER,
    (string)$database->DATABASE_PASSWORD,
    (string)$database->DATABASE_PREFIX,
    (string)$database->DATABASE_HOST,
    (int)$database->DATABASE_PORT
  );
}

function generate_entity_manager(Connection $connection, Cache $cache): EntityManager
{
  $entityManager = new EntityManager($connection, $cache);
  $entityManager->addPath(PATH_BASE . '/doctrine');

  return $entityManager;
}

function generate_renderer(Config $config): Renderer
{
  switch ((string)$config->open('core')->CORE_RENDERER) {
    case 'twig':
      $twig = $config->open('renderer/twig');
      $driver = new TwigDriver((bool)$twig->TWIG_CACHE);
      break;

    case 'smarty':
      $smarty = $config->open('renderer/smarty');
      $driver = new SmartyDriver(
        (bool)$smarty->SMARTY_CACHE,
        (bool)$smarty->SMARTY_FORCE_COMPILE
      );
      break;

    default:
      $driver = new PhpDriver();
      break;
  }

  $renderer = new Renderer($driver);
  $renderer->addPath(PATH_BASE . '/views');

  return $renderer;
}

function generate_mailer(Config $config, Renderer $renderer): Mailer
{
  switch ((string)$config->open('core')->CORE_MAILER) {
    default:
      $driver = new MailDriver($renderer);
      break;
  }

  return new Mailer($driver);
}

function generate_session(Config $config, Cache $cache): Session
{
  switch ((string)$config->open('core')->CORE_SESSION) {
    case 'cache':
      $driver = new CacheDriver($cache, (string)$config->open('core')->CORE_SESSION_NAME);
      break;

    default:
      $driver = new NativeDriver((string)$config->open('core')->CORE_SESSION_NAME);
      break;
  }

  return new Session($driver);
}

/**
* Bootstrap application.
*/
// phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
register_bootstrap(function (RouteContainer $routeContainer, ServiceContainer $serviceContainer) {
  $cache = generate_cache();
  $config = generate_config($cache);

  /**
   * Register error handler.
   */
  if ((bool)$config->open('core')->CORE_DEBUG === true) {
    error_reporting(E_ALL);

    $whoops = new Whoops();
    $whoops->pushHandler(new PrettyPageHandler());
    $whoops->register();
  }

  $connection = generate_connection($config);
  $entityManager = generate_entity_manager($connection, $cache);
  $renderer = generate_renderer($config);
  $mailer = generate_mailer($config, $renderer);
  $session = generate_session($config, $cache);

  $serviceContainer->setService($cache);
  $serviceContainer->setService($config);
  $serviceContainer->setService($connection);
  $serviceContainer->setService($entityManager);
  $serviceContainer->setService($renderer);
  $serviceContainer->setService($mailer);
  $serviceContainer->setService($session);

  $serviceContainer->setService(new ImageManager());
  $serviceContainer->setService(new FileManager());
});
