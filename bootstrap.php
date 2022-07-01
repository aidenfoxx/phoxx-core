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
use Phoxx\Core\Http\Router;
use Phoxx\Core\Mailer\Drivers\MailDriver;
use Phoxx\Core\Mailer\Mailer;
use Phoxx\Core\Renderer\Drivers\PhpDriver;
use Phoxx\Core\Renderer\Drivers\SmartyDriver;
use Phoxx\Core\Renderer\Drivers\TwigDriver;
use Phoxx\Core\Renderer\Renderer;
use Phoxx\Core\Session\Drivers\CacheDriver;
use Phoxx\Core\Session\Drivers\NativeDriver;
use Phoxx\Core\Session\Session;
use Phoxx\Core\System\Config;
use Phoxx\Core\System\Services;

if (!function_exists('register_bootstrap')) {
  return;
}

function generate_cache(): Cache
{
  $config = new Config();
  $config->addPath(PATH_BASE . '/config');

  switch ($config->open('core')->CORE_CACHE) {
    case 'apcu':
      $cache = new ApcuDriver();
      break;

    case 'memcached':
      $cache = new MemcachedDriver();
      foreach ((array)$config->open('cache/memcached')->MEMCACHED_HOSTS as $host) {
        $cache->addServer((string)$host[0], (int)$host[1], (int)$host[2]);
      }
      break;

    case 'redis':
      $cache = new RedisDriver((string)$config->open('cache/redis')->REDIS_HOST, (int)$redis->REDIS_PORT);
      break;

    case 'file':
      $cache = new FileDriver(PATH_CACHE . '/core');
      break;

    default:
      $cache = new ArrayDriver();
      break;
  }

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
  switch ($config->open('core')->CORE_RENDERER) {
    case 'twig':
      $twig = $config->open('renderer/twig');
      $renderer = new TwigDriver((bool)$twig->TWIG_CACHE);
      break;

    case 'smarty':
      $smarty = $config->open('renderer/smarty');
      $renderer = new SmartyDriver(
        (bool)$smarty->SMARTY_CACHE,
        (bool)$smarty->SMARTY_FORCE_COMPILE
      );
      break;

    default:
      $renderer = new PhpDriver();
      break;
  }

  $renderer->addPath(PATH_BASE . '/views');

  return $renderer;
}

function generate_mailer(Config $config, Renderer $renderer): Mailer
{
  switch ($config->open('core')->CORE_MAILER) {
    default:
      $mailer = new MailDriver($renderer);
      break;
  }

  return $mailer;
}

function generate_session(Config $config, Cache $cache): Session
{
  switch ($config->open('core')->CORE_SESSION) {
    case 'cache':
      $session = new CacheDriver($cache, (string)$config->open('core')->CORE_SESSION_NAME);
      break;

    default:
      $session = new NativeDriver((string)$config->open('core')->CORE_SESSION_NAME);
      break;
  }

  return $session;
}

/**
* Bootstrap application.
*/
register_bootstrap(function (Router $_router, Services $services) {
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

  $services->setService($cache, Cache::class);
  $services->setService($config);
  $services->setService($connection);
  $services->setService($entityManager);
  $services->setService($renderer, Renderer::class);
  $services->setService($mailer, Mailer::class);
  $services->setService($session, Mailer::class);

  $services->setService(new FileManager());
});
