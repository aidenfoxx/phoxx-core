<?php

use Whoops\Run as Whoops;
use Whoops\Handler\PrettyPageHandler;

use Phoxx\Core\Utilities\Config;
use Phoxx\Core\Cache\Cache;
use Phoxx\Core\Cache\Drivers\ApcuDriver;
use Phoxx\Core\Cache\Drivers\ArrayDriver;
use Phoxx\Core\Cache\Drivers\FileDriver;
use Phoxx\Core\Cache\Drivers\MemcachedDriver;
use Phoxx\Core\Cache\Drivers\RedisDriver;
use Phoxx\Core\Database\Doctrine;
use Phoxx\Core\Database\Migrator;
use Phoxx\Core\Renderer\Renderer;
use Phoxx\Core\Renderer\Drivers\PhpDriver;
use Phoxx\Core\Renderer\Drivers\SmartyDriver;
use Phoxx\Core\Renderer\Drivers\TwigDriver;
use Phoxx\Core\Mailer\Mailer;
use Phoxx\Core\Mailer\Drivers\MailDriver;
use Phoxx\Core\Mailer\Drivers\SmtpDriver;
use Phoxx\Core\Http\Session\Session;
use Phoxx\Core\Http\Session\Drivers\CacheDriver;
use Phoxx\Core\Http\Session\Drivers\NativeDriver;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Http\Helpers\SimpleRequest;
use Phoxx\Core\Framework\Application;

/**
 * Service generators.
 */
function newCacheService(): Cache
{
	$config = new Config();
	$config->addPath(PATH_CORE.'/config');

	switch ((string)$config->getFile('core')->CORE_CACHE)
	{
		case 'apcu':
			$driver = new ApcuDriver();
			break;

		case 'memcached':
			$driver = new MemcachedDriver();
			foreach ((array)$config->getFile('cache/memcached')->MEMCACHED_SERVERS as $server) {
				$driver->addServer((string)$server[0], (int)$server[1], (int)$server[2]);
			}
			break;

		case 'redis':
			$driver = new RedisDriver((string)$config->getFile('cache/redis')->REDIS_HOST, (int)$redis->REDIS_PORT);
			break;

		case 'file':
			$driver = new FileDriver(PATH_CACHE.'/core');
			break;

		default:
			$driver = new ArrayDriver();
			break;
	}

	$cache = new Cache($driver);

	return $cache;
}

function newConfigService(Cache $cache): Config
{
	$config = new Config($cache);
	$config->addPath(PATH_CORE.'/config');

	return $config;
}

function newDoctrineService(Config $config, Cache $cache): Doctrine
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
	$doctrine->addPath(PATH_CORE.'/doctrine');

	return $doctrine;
}

function newMigrationService(): Migrator
{
	return new Migrator();
}

function newRendererService(Config $config): Renderer
{
	switch ((string)$config->getFile('core')->CORE_RENDERER)
	{
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
	$renderer->addPath(PATH_CORE.'/views');

	return $renderer;
}

function newMailerService(Config $config, Renderer $renderer): Mailer
{
	switch ((string)$config->getFile('core')->CORE_MAILER)
	{
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

function newSessionService(Config $config)
{
	switch ((string)$config->getFile('core')->CORE_SESSION)
	{
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
register_action('bootstrap', function() {

	/**
	 * Core services.
	 */
	$cache = newCacheService();
	$config = newConfigService($cache);

	/**
	 * Error handling.
	 */
	if ((bool)$config->getFile('core')->CORE_DEBUG === true) {
		error_reporting(E_ALL);
		ini_set('display_errors', E_ALL);

		$whoops = new Whoops();
		$whoops->pushHandler(new PrettyPageHandler());
		$whoops->register();
	} else {
		error_reporting(E_ERROR);
		ini_set('display_errors', 0);

		/**
		 * Error handler.
		 */
		register_shutdown_function(function() {
			if (error_get_last() !== null) {
				/**
				 * Handle request exceptions.
				 */
				$request = new SimpleRequest('_500_');

				if (($response = Application::getInstance('core')->dispatch($request)) instanceof Response) {
					Application::getInstance('core')->send($response);
					exit;
				} else {
					http_response_code(500);
					echo '<h1>Error 500</h1><p>An unknown error has occured.</p>';
					exit;
				}
			}
		});
	}

	/**
	 * Application services.
	 */
	$doctrine = newDoctrineService($config, $cache);
	$migrator = newMigrationService();
	$renderer = newRendererService($config);
	$mailer = newMailerService($config, $renderer);
	$session = newSessionService($config);

	/**
	 * Assemble application.
	 */
	$serviceContainer = Application::getInstance('core')->getServiceContainer();

	$serviceContainer->setService($config);
	$serviceContainer->setService($cache);
	$serviceContainer->setService($doctrine);
	$serviceContainer->setService($migrator);
	$serviceContainer->setService($renderer);
	$serviceContainer->setService($mailer);
	$serviceContainer->setService($session);

	/**
	 * Bind packages to services.
	 */
	$application = Application::getInstance();
	foreach ((array)$config->getFile('packages') as $package) {
		// if (is_file(PATH_PACKAGES.'/'.$package.'/bootstrap.php') === false) {
		// 	throw new Exception('Failed to initialize package `'.$package.'`.');
		// }
		//
		// $config->addPath(PATH_PACKAGES.'/'.$package.'/config', $package);
		// $renderer->addPath(PATH_PACKAGES.'/'.$package.'/views', $package);
		// $doctrine->addPath(PATH_PACKAGES.'/'.$package.'/doctrine');

		trigger_action('bootstrap_package', array($application));
	}

	/**
	 * Dispatch the default HTTP request.
	 */
	$request = new SimpleRequest($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

	if (($response = Application::getInstance('core')->dispatch($request)) instanceof Response) {
		Application::getInstance('core')->send($response);
		exit;
	}

	/**
	 * Dispatch 404 request.
	 */
	$request = new SimpleRequest('_404_');

	if (($response = Application::getInstance('core')->dispatch($request)) instanceof Response) {
		Application::getInstance('core')->send($response);
		exit;
	} else {
		http_response_code(404);
		echo '<h1>Error 404</h1><p>The requested page could not be found.</p>';
		exit;
	}
});