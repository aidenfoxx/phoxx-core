<?php

namespace Phoxx\Core\Http;

class Request
{
  protected $url;

  protected $query;

  protected $request;

  protected $server;

  protected $cookies;

  protected $files;

  protected $content;

  public function __construct(
    string $uri,
    string $method = 'GET',
    array $query = [],
    array $request = [],
    array $server = [],
    array $cookies = [],
    array $files = [],
    ?string $content = null
  ) {
    // Define required application variables
    $server = array_replace([
      'PATH_INFO' => '',
      'SERVER_NAME' => 'localhost',
      'SERVER_PORT' => 80,
      'SERVER_PROTOCOL' => 'HTTP/1.1',
      'HTTP_HOST' => 'localhost',
      'HTTP_USER_AGENT' => 'Phoxx/1.x',
      'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
      'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5',
      'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
      'REMOTE_ADDR' => '127.0.0.1',
      'SCRIPT_NAME' => '',
      'SCRIPT_FILENAME' => '',
      'REQUEST_TIME' => time()
    ], $server);

    // NOTE: Fix for paths starting with "//" returning incorrect host
    $components = parse_url(preg_replace('#^\/\/#', '/', $uri));

    // Parse out URI data into server
    if (isset($components['path'])) {
      $server['PATH_INFO'] = $components['path'] !== '/' ? $components['path'] : '';
    }

    if (isset($components['scheme'])) {
      if (strtoupper($components['scheme']) === 'HTTPS') {
        $server['HTTPS'] = 'on';
        $server['SERVER_PORT'] = 443;
      } else {
        unset($server['HTTPS']);
        $server['SERVER_PORT'] = 80;
      }
    }

    if (isset($components['host'])) {
      $server['SERVER_NAME'] = $components['host'];
      $server['HTTP_HOST'] = $components['host'];
    }

    if (isset($components['port'])) {
      $server['SERVER_PORT'] = $components['port'];
      $server['HTTP_HOST'] .= !isset($server['HTTPS']) && $components['port'] !== 80 || isset($server['HTTPS']) && $components['port'] !== 443 ? ':' . $components['port'] : '';
    }

    if (isset($components['user'])) {
      $server['PHP_AUTH_USER'] = $components['user'];
    }

    if (isset($components['pass'])) {
      $server['PHP_AUTH_PW'] = $components['pass'];
    }

    if (isset($components['query'])) {
      parse_str(html_entity_decode($components['query']), $uriQuery);

      if (!empty($query)) {
        $query = array_replace($uriQuery, $query);
        $server['QUERY_STRING'] = http_build_query($query, '', '&');
      } else {
        $query = $uriQuery;
        $server['QUERY_STRING'] = $components['query'];
      }
    } elseif (!empty($query)) {
      $server['QUERY_STRING'] = http_build_query($query, '', '&');
    }

    if (strtoupper($method) === 'POST') {
      $server['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
    }

    $server['REQUEST_METHOD'] = strtoupper($method);
    $server['REQUEST_URI'] = ($server['PATH_INFO'] ?? '/') . (isset($server['QUERY_STRING']) ? '?' . $server['QUERY_STRING'] : '');

    // Resolve PATH_INFO to URL rewrite
    if (strlen($basePath = @dirname($server['SCRIPT_NAME'])) > 1 && strpos($server['PATH_INFO'], $basePath) === 0) {
      $server['PATH_INFO'] = ($path = substr($server['PATH_INFO'], strlen($basePath))) !== '/' ? $path : '';
    }

    $this->query = $query;
    $this->request = $request;
    $this->server = $server;
    $this->cookies = $cookies;
    $this->files = $files;
    $this->content = $content;
  }

  public function getPath(): string
  {
    return $this->server['PATH_INFO'];
  }

  public function getBasePath(): string
  {
    return strlen($basePath = @dirname($this->server['SCRIPT_NAME'])) > 1 ? $basePath : '';
  }

  public function getUri(): string
  {
    return $this->server['REQUEST_URI'];
  }

  public function getUrl(): string
  {
    if (!$this->url) {
      $this->url = isset($this->server['HTTPS']) ? 'https://' : 'http://';
      $this->url .= $this->server['SERVER_NAME'];
      $this->url .= !isset($this->server['HTTPS']) && (int)$this->server['SERVER_PORT'] !== 80 || isset($this->server['HTTPS']) && (int)$this->server['SERVER_PORT'] !== 443 ? ':' . $this->server['SERVER_PORT'] : '';
    }

    return $this->url;
  }

  public function getMethod(): string
  {
    return $this->server['REQUEST_METHOD'];
  }

  public function getQuery(string $index)
  {
    return $this->query[$index] ?? null;
  }

  public function getRequest(string $index)
  {
    return $this->request[$index] ?? null;
  }

  public function getServer(string $index)
  {
    return $this->server[$index] ?? null;
  }

  public function getCookie(string $index): ?string
  {
    return $this->cookies[$index] ?? null;
  }

  public function getFile(string $index): ?array
  {
    return (array)$this->files[$index] ?? null;
  }

  public function getContent(): ?string
  {
    return $this->content;
  }
}
