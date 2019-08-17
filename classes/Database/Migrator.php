<?php

namespace Phoxx\Core\Database;

use Phoxx\Core\Database\Migration;
use Phoxx\Core\Database\Exceptions\MigrationException;
use Phoxx\Core\Framework\Interfaces\ServiceProvider;

class Migrator implements ServiceProvider
{
	protected $migrations = array();

	public function __construct()
	{
		$migrations = PATH_CACHE.'/migrations.php';

		if (is_file($migrations) === true) {
			$this->migrations = include $migrations;
		}
		
		/**
		 * Check validitiy of migration data and 
		 * fix if required.
		 */
		if (is_array($this->migrations) === false) {
			$this->migrations = array();
		}
	}

	public function __destruct()
	{
		file_put_contents(PATH_CACHE.'/migrations.php', '<?php return '.var_export($this->migrations, true).';');
	}

	public function getServiceName(): string
	{
		return 'migrator';
	}

	public function active(Migration $migration): bool
	{
		return isset($this->migrations[get_class($migration)]);
	}

	public function up(Migration $migration): void
	{
		if ($this->active($migration) === true) {
			return;
		}

		if ($migration->up() === false) {
			throw new MigrationException('Failed to install migration `'.get_class($migration).'`.');
		}

		$this->migrations[get_class($migration)] = array('step' => count($this->migrations), 'timestamp' => time());
	}

	public function down(Migration $migration): void
	{
		if ($this->active($migration) === false) {
			return;
		}

		if ($migration->down() === false) {
			throw new MigrationException('Failed to remove migration `'.get_class($migration).'`.');
		}

		unset($this->migrations[get_class($migration)]);
	}
}