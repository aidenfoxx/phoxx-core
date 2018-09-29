<?php

namespace Phoxx\Core\Database;

use Phoxx\Core\Database\Migration;
use Phoxx\Core\Database\Exceptions\MigrationException;

class Migrator
{
	protected static $instance;

	public static function core(): self
	{
		if (isset(static::$instance) === false) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	protected $path;

	protected $migrations = array();

	public function __construct()
	{
		$this->path = PATH_CACHE.'/migrations.php';

		if (file_exists($this->path) === true) {
			$this->migrations = include($this->path);

			/**
			 * If migration map doesn't exist, or is not a valid 
			 * array, set to array.
			 */
			if (is_array($this->migrations) === false) {
				$this->migrations = array();
			}
		}
	}

	public function __destruct()
	{
		file_put_contents($this->path, '<?php return '.var_export($this->migrations, true).';');
	}

	public function active(Migration $migration): bool
	{
		return isset($this->migrations[get_class($migration)]);
	}

	public function up(Migration $migration): void
	{
		if ($this->active($migration) === true) {
			$this->migrations[get_class($migration)]['date_updated'] = time();
			return;
		}

		if ($migration->up() === true) {
			$this->migrations[get_class($migration)] = array('date_created' => time(), 'date_updated' => time());
			return;
		}
		
		throw new MigrationException('Failed to install migration `'.get_class($migration).'`.');
	}

	public function down(Migration $migration): void
	{
		if ($this->active($migration) === false) {
			return;
		}

		if ($migration->down() === true) {
			unset($this->migrations[get_class($migration)]);
			return;
		}

		throw new MigrationException('Failed to remove migration `'.get_class($migration).'`.');
	}
}