<?php

namespace Phoxx\Core\Database;

use Phoxx\Core\Utilities\Validator;

abstract class Model
{
	private $validator;
	
	protected $id;

	protected $dateCreated = 0;

	protected $dateUpdated = 0;

	public function __construct()
	{
		$this->validator = new Validator();
	}

	public function getErrors(): array
	{
		return $this->validator->getErrors();
	}

	public function getId(): int
	{
		return (int)$this->id;
	}

	public function getDateCreated(): int
	{
		return $this->dateCreated;
	}

	public function getDateUpdated(): int
	{
		return $this->dateUpdated;
	}

	public function setDateCreated(int $date): void
	{
		$this->dateCreated = $date;
	}

	public function setDateUpdated(int $date): void
	{
		$this->dateUpdated = $date;
	}
}