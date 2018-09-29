<?php

namespace Phoxx\Core\Http;

class Request
{
	protected $query = array();

	protected $request = array();

	protected $server = array();

	protected $cookies = array();

	protected $files = array();

	protected $content;

	/**
	 * TODO: Maybe add session.
	 */
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
		return $this->getServer('REQUEST_METHOD');
	}

	public function getPath(): string
	{
		return $this->getServer('PATH_INFO');
	}

	public function getUri(): string
	{
		return $this->getServer('REQUEST_URI');
	}

	public function getBaseUrl(): string
	{
		/**
		 * TODO: Implement.
		 */
	}

	public function getQuery(string $param)
	{
		return isset($this->query[$param]) === true ? $this->query[$param] : null;
	}

	public function getRequest(string $param)
	{
		return isset($this->request[$param]) === true ? $this->request[$param] : null;
	}

	public function getServer(string $param): ?string
	{
		return isset($this->server[$param]) === true ? (string)$this->server[$param] : null;
	}

	public function getCookie(string $param): ?string
	{
		return isset($this->cookies[$param]) === true ? (string)$this->cookies[$param] : null;
	}

	public function getFile(string $param): ?array
	{
		return isset($this->files[$param]) === true ? (array)$this->files[$param] : null;
	}

	public function getContent(): ?string
	{
		return $this->content;
	}
}