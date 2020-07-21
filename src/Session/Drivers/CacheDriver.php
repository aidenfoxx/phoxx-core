<?php

namespace Phoxx\Core\Session\Drivers;

use Phoxx\Core\Cache\Cache;
use Phoxx\Core\Session\Interfaces\SessionDriver;

class CacheDriver implements SessionDriver
{
  private const PREFIX = 'sid_';

  private $cache;

  protected $sessionName;

  protected $sessionId;

  protected $active = false;

  public function __construct(Cache $cache, ?string $sessionName = null)
  {
    $this->cache = $cache;
    $this->sessionName = $sessionName !== null ? $sessionName : ini_get('session.name');
  }

  public function getValue(string $index)
  {
    if ($this->active === false) {
      return null;
    }

    $sessionData = (array)$this->cache->getValue(self::PREFIX . $this->sessionId);

    return isset($sessionData[$index]) === true ? $sessionData[$index] : null;
  }

  public function setValue(string $index, $value): void
  {
    if ($this->active === true) {
      $sessionData = (array)$this->cache->getValue(self::PREFIX . $this->sessionId);
      $sessionData[$index] = $value;

      $this->cache->setValue(self::PREFIX . $this->sessionId, $sessionData, (int)ini_get('session.cookie_lifetime'));
    }
  }

  public function removeValue(string $index): void
  {
    if ($this->active === true) {
      $sessionData = (array)$this->cache->getValue(self::PREFIX . $this->sessionId);

      unset($sessionData[$index]);

      $this->cache->setValue(self::PREFIX . $this->sessionId, $sessionData, (int)ini_get('session.cookie_lifetime'));
    }
  }

  public function active(): bool
  {
    return $this->active;
  }

  public function open(): void
  {
    if ($this->active === true) {
      return;
    }

    if (isset($_COOKIE[$this->sessionName]) === false) {
      $_COOKIE[$this->sessionName] = session_create_id();
    }

    $this->sessionId = $_COOKIE[$this->sessionName];
    $this->active = true;

    setcookie(
      $this->sessionName,
      $this->sessionId,
      (int)ini_get('session.cookie_lifetime'),
      ini_get('session.cookie_path'),
      ini_get('session.cookie_domain'),
      (bool)ini_get('session.cookie_secure'),
      (bool)ini_get('session.cookie_httponly')
    );
  }

  public function close(): void
  {
    $this->active = false;
  }

  public function regenerate(): void
  {
    if ($this->active === true) {
      $sessionData = (array)$this->cache->getValue(self::PREFIX . $this->sessionId);

      $_COOKIE[$this->sessionName] = session_create_id();

      $this->cache->removeValue(self::PREFIX . $this->sessionId);
      $this->sessionId = $_COOKIE[$this->sessionName];
      $this->cache->setValue(self::PREFIX . $this->sessionId, $sessionData, (int)ini_get('session.cookie_lifetime'));
    }
  }

  public function clear(): void
  {
    if ($this->active === true) {
      $this->cache->removeValue(self::PREFIX . $this->sessionId);
    }
  }
}
