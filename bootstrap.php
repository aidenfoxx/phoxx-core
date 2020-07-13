<?php

use Whoops\Run as Whoops;
use Whoops\Handler\PrettyPageHandler;

use Phoxx\Core\Cache\Cache;
use Phoxx\Core\Cache\Drivers\ApcuDriver;
use Phoxx\Core\Cache\Drivers\ArrayDriver;
use Phoxx\Core\Cache\Drivers\FileDriver;
use Phoxx\Core\Cache\Drivers\MemcachedDriver;
use Phoxx\Core\Cache\Drivers\ReditDriver;
use Phoxx\Core\Database\Doctrine;
use Phoxx\Core\File\FileManager;
use Phoxx\Core\File\ImageManager;
use Phoxx\Core\Framework\Application;
use Phoxx\Core\Mailer\Drivers\MailDriver;
use Phoxx\Core\Mailer\Mailer;
use Phoxx\Core\Renderer\Drivers\PhpDriver;
use Phoxx\Core\Renderer\Drivers\SmartyDriver;
use Phoxx\Core\Renderer\Drivers\TwigDriver;
use Phoxx\Core\Renderer\Renderer;
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

  switch ((string)$config->getFile('core')->CORE_CACHE) {
    case 'apcu':
      $driver = new ApcuDriver();
      break;

    case 'memcached':
      $driver = new MemcachedDriver();
      foreach ((array)$config->getFile('cache/memcached')->MEMCACHED_HOSTS as $host) {
        $driver->addServer((string)$host[0], (int)$host[1], (int)$host[2]);
      }
      break;

    case 'redis':
      $driver = new RedisDriver((string)$config->getFile('cache/redis')->REDIS_HOST, (int)$redis->REDIS_PORT);
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

function generate_doctrine(Config $config, Cache $cache): Doctrine
{
  $database = $config->getFile('database');

  $doctrine = new Doctrine(
    (string)$database->DATABASE_NAME,
    (string)$database->DATABASE_USER,
    (string)$database->DATABASE_PASSWORD,
    (string)$database->DATABASE_PREFIX,
    (string)$database->DATABASE_HOST,
    (int)$database->DATABASE_PORT,
    $cache
  );
  $doctrine->addPath(PATH_BASE . '/doctrine');

  return $doctrine;
}

function generate_renderer(Config $config): Renderer
{
  switch ((string)$config->getFile('core')->CORE_RENDERER) {
    case 'twig':
      $twig = $config->getFile('renderer/twig');
      $driver = new TwigDriver((bool)$twig->TWIG_CACHE);
      break;

    case 'smarty':
      $smarty = $config->getFile('renderer/smarty');
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
  switch ((string)$config->getFile('core')->CORE_MAILER) {
    case 'smtp':
      $smtp =  $config->getFile('mailer/smtp');
      $driver = new SmtpDriver(
        $renderer,
        (string)$smtp->SMTP_HOST,
        (int)$smtp->SMTP_PORT,
        (bool)$smtp->SMTP_SSL,
        (bool)$smtp->SMTP_AUTH,
        (string)$smtp->SMTP_USER,
        (string)$smtp->SMTP_PASSWORD
      );
      break;

    default:
      $driver = new MailDriver($renderer);
      break;
  }

  return new Mailer($driver);
}

function generate_session(Config $config): Session
{
  switch ((string)$config->getFile('core')->CORE_SESSION) {
    case 'cache':
      $driver = new CacheDriver((string)$config->getFile('core')->CORE_SESSION_NAME);
      break;

    default:
      $driver = new NativeDriver((string)$config->getFile('core')->CORE_SESSION_NAME);
      break;
  }

  return new Session($driver);
}

/**
* Bootstrap application.
*/
register_bootstrap(function (Application $application) {
  $cache = generate_cache();
  $config = generate_config($cache);

  /**
   * Register error handler.
   */
  if ((bool)$config->getFile('core')->CORE_DEBUG === true) {
    error_reporting(E_ALL);

    $whoops = new Whoops();
    $whoops->pushHandler(new PrettyPageHandler());
    $whoops->register();
  }

  $doctrine = generate_doctrine($config, $cache);
  $renderer = generate_renderer($config);
  $mailer = generate_mailer($config, $renderer);
  $session = generate_session($config);

  $application->getServiceContainer()->addService($cache);
  $application->getServiceContainer()->addService($config);
  $application->getServiceContainer()->addService($doctrine);
  $application->getServiceContainer()->addService($renderer);
  $application->getServiceContainer()->addService($mailer);
  $application->getServiceContainer()->addService($session);
});
