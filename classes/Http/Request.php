<?php

namespace Phoxx\Core\Http;

class Request
{
	protected $baseUri;

	protected $query = array();

	protected $request = array();

	protected $server = array();

	protected $cookies = array();

	protected $files = array();

	protected $content;

	public function __construct(
		string $uri,
		string $method = 'GET',
		array $query = array(), 
		array $request = array(), 
		array $server = array(),
		array $cookies = array(),
		array $files = array(),
		string $content = null)
	{
		/**
		 * Define required application variables.
		 */
		$server = array_replace(array(
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
		), $server);

		$components = parse_url($uri);

		/**
		 * Parse out URI data into session.
		 */
		$server['PATH_INFO'] = $components['path'] !== '/' ? $components['path'] : '';

		if (isset($components['scheme']) === true) {
			if ($components['scheme'] === 'https') {
				$server['HTTPS'] = 'on';
				$server['SERVER_PORT'] = 443;
			} else {
				unset($this->server['HTTPS']);
				$server['SERVER_PORT'] = 80;
			}
		}

		if (isset($components['host']) === true) {
			$server['SERVER_NAME'] = $components['host'];
			$server['HTTP_HOST'] = $components['host'];
		}

		if (isset($components['port']) === true) {
			$server['SERVER_PORT'] = $components['port'];
			$server['HTTP_HOST'] = $server['HTTP_HOST'].':'.$components['port'];
		}

		if (isset($components['user']) === true) {
			$server['PHP_AUTH_USER'] = $components['user'];
		}

		if (isset($components['pass']) === true) {
			$server['PHP_AUTH_PW'] = $components['pass'];
		}

		if (isset($components['query']) === true) {
			parse_str(html_entity_decode($components['query']), $queryData);

			if (empty($query) === false) {
				$query = array_replace($queryData, $query);
				$server['QUERY_STRING'] = http_build_query($query, '', '&');
			} else {
				$query = $queryData;
				$server['QUERY_STRING'] = $components['query'];
			}
		} elseif (empty($query) === false) {
			$server['QUERY_STRING'] = http_build_query($query, '', '&');
		}

		if (strtoupper($method) === 'POST') {
			$server['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
		}

		$server['REQUEST_METHOD'] = strtoupper($method);
		$server['REQUEST_URI'] = (empty($server['PATH_INFO']) === false ? $server['PATH_INFO'] : '/').(empty($server['QUERY_STRING']) === false ? '?'.$server['QUERY_STRING'] : '');

		/**
		 * Resolve PATH_INFO for internal requests.
		 */
		if (strcasecmp($server['SERVER_NAME'], $_SERVER['SERVER_NAME']) === 0) {
			$server['PATH_INFO'] = ($path = substr($server['PATH_INFO'], strlen(PATH_PUBLIC))) !== '/' ? $path : '';
		}

		$this->query = $query;
		$this->request = $request;
		$this->server = $server;
		$this->cookies = $cookies;
		$this->files = $files;
		$this->content = $content;
	}

	public function getMethod(): string
	{
		return $this->server['REQUEST_METHOD'];
	}

	public function getPath(): string
	{
		return $this->server['PATH_INFO'];
	}

	public function getUri(): string
	{
		return $this->server['REQUEST_URI'];
	}

	public function getBaseUri(): string
	{
		if (isset($this->baseUri) === false) {
			$protocol = isset($this->server['HTTPS']) === true ? 'https://' : 'http://';
			$host = $this->server['SERVER_NAME'];
			$port = (int)$this->server['SERVER_PORT'] === 80 || (int)$this->server['SERVER_PORT'] === 443 && isset($this->server['HTTPS']) === true ? '' : ':'.$this->server['SERVER_PORT'];

			$this->baseUri = $protocol.$host.$port;
		}
		return $this->baseUri;
	}

	public function getQuery(string $index)
	{
		return isset($this->query[$index]) === true ? $this->query[$index] : null;
	}

	public function getRequest(string $index)
	{
		return isset($this->request[$index]) === true ? $this->request[$index] : null;
	}

	public function getServer(string $index)
	{
		return isset($this->server[$index]) === true ? $this->server[$index] : null;
	}

	public function getCookie(string $index): ?string
	{
		return isset($this->cookies[$index]) === true ? (string)$this->cookies[$index] : null;
	}

	public function getFile(string $index): ?array
	{
		return isset($this->files[$index]) === true ? (array)$this->files[$index] : null;
	}

	public function getContent(): ?string
	{
		return $this->content;
	}
}