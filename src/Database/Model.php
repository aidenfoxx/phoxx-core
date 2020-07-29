<?php

namespace Phoxx\Core\Database;

use Phoxx\Core\Utilities\Validator;

abstract class Model
{
  private $validator;

  protected $dateCreated = 0;

  protected $dateUpdated = 0;

  public function __construct()
  {
    $this->validator = new Validator();
  }

  public function getValidator(): Validator
  {
    return $this->validator;
  }

  public function getDateCreated(): int
  {
    return $this->dateCreated;
  }

  public function setDateCreated(int $date): void
  {
    $this->dateCreated = $date;
  }

  public function getDateUpdated(): int
  {
    return $this->dateUpdated;
  }

  public function setDateUpdated(int $date): void
  {
    $this->dateUpdated = $date;
  }
}
