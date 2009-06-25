<?php

require_once 'JFramework/Controller/Request/jfRequestBase.php';

class jfRequestHttp extends jfRequestBase {

	const SCHEME_HTTP  = 'http';
	const SCHEME_HTTPS = 'https';
	
	protected $_requestUri;
	protected $_baseUrl = null;
	protected $_basePath = null;
	protected $_pathInfo = '';
	
	protected $_params = array();
	
	
	public function __construct() {
		$this->initRequestUri();
    }
	public function getRequestUri() {
		if (empty($this->_requestUri)) {
			$this->initRequestUri();
		}

		return $this->_requestUri;
	}

	public function getBaseUrl() {
		if (null === $this->_baseUrl) {
			$this->initBaseUrl();
		}
		return $this->_baseUrl;
	}
	public function setBaseUrl($baseUrl = null) {
		if (!is_string($baseUrl)) return $this;

		$this->_baseUrl = rtrim($baseUrl, '/');
		return $this;
	}

	public function getBasePath() {
		if (null === $this->_basePath) {
			$this->initBasePath();
		}
		return $this->_basePath;
	}

	public function getPathInfo() {
		if (empty($this->_pathInfo)) {
			$this->initPathInfo();
		}
		return $this->_pathInfo;
	}

    public function get($key) {
		switch (true) {
			case isset($this->_params[$key]):
				return $this->_params[$key];
			case isset($_GET[$key]):
				return $_GET[$key];
			case isset($_POST[$key]):
				return $_POST[$key];
			case isset($_COOKIE[$key]):
				return $_COOKIE[$key];
			case ($key == 'REQUEST_URI'):
				return $this->getRequestUri();
			case ($key == 'PATH_INFO'):
				return $this->getPathInfo();
			case isset($_SERVER[$key]):
				return $_SERVER[$key];
			case isset($_ENV[$key]):
				return $_ENV[$key];
			default:
				return null;
		}
    }
    
	public function getQuery($key = null, $default = null) {
		if (null === $key) {
			return $_GET;
		}

		return (isset($_GET[$key])) ? $_GET[$key] : $default;
	}
	
	public function getPost($key = null, $default = null) {
		if (null === $key) {
			return $_POST;
		}

		return (isset($_POST[$key])) ? $_POST[$key] : $default;
	}
	
	public function getCookie($key = null, $default = null) {
		if (null === $key) {
			return $_COOKIE;
		}

		return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : $default;
	}
	
	public function getServer($key = null, $default = null) {
		if (null === $key) {
			return $_SERVER;
		}

		return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
	}

    public function getEnv($key = null, $default = null) {
		if (null === $key) {
			return $_ENV;
		}

		return (isset($_ENV[$key])) ? $_ENV[$key] : $default;
	}
	
	public function setParam($key, $value) {
		parent::setParam($key, $value);
		return $this;
	}
	public function setParams(array $params) {
		foreach ($params as $key => $value) {
			$this->setParam($key, $value);
		}
		return $this;
	}
	public function getParam($key, $default = null) {
		return (isset($this->_params[$key])) ? $this->_params[$key] : $default;
	}
	public function getParams() {
		return $this->_params;
	}
	
	public function getMethod() {
		return $this->getServer('REQUEST_METHOD');
	}
	public function isPost()  {
		return 'POST' == $this->getMethod();
    }
    public function isGet() {
    	return 'GET' == $this->getMethod();
    }
	public function isXmlHttpRequest() {
		return ($this->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
	}
	
	public function getHeader($header) {
		$temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
		if (!empty($_SERVER[$temp])) {
			return $_SERVER[$temp];
		}

		// This seems to be the only way to get the Authorization header on Apache
		if (function_exists('apache_request_headers')) {
			$headers = apache_request_headers();
			if (!empty($headers[$header])) {
				return $headers[$header];
			}
		}

		return false;
	}
	
	public function getScheme() {
		return ($this->getServer('HTTPS') == 'on') ? self::SCHEME_HTTPS : self::SCHEME_HTTP;
	}
	
    /**
     * 初始化请求的uri
     *
     * @param string $requestUri
     * @return unknown
     */
	private function initRequestUri() {
		if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
			$requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
		} elseif (isset($_SERVER['REQUEST_URI'])) {
			$requestUri = $_SERVER['REQUEST_URI'];
		} elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
			$requestUri = $_SERVER['ORIG_PATH_INFO'];
			if (!empty($_SERVER['QUERY_STRING'])) {
				$requestUri .= '?' . $_SERVER['QUERY_STRING'];
			}
		} else {
			return $this;
		}

		$this->_requestUri = $requestUri;
		return $this;
	}
	
	/**
	 * 初始化请求的根url
	 *
	 * @return void
	 */
	private function initBaseUrl() {
		$filename = basename($this->getServer('SCRIPT_FILENAME')); //ab file path

		if (basename($this->getServer('SCRIPT_NAME')) === $filename) {
			$baseUrl = $this->getServer('SCRIPT_NAME'); //rel file path
		} elseif (basename($this->getServer('PHP_SELF')) === $filename) {
			$baseUrl = $this->getServer('PHP_SELF'); //same to script_name
		} elseif (basename($this->getServer('ORIG_SCRIPT_NAME')) === $filename) {
			$baseUrl = $this->getServer('ORIG_SCRIPT_NAME'); // 1and1 shared hosting compatibility
		} else {
			// Backtrack up the script_filename to find the portion matching php_self
			$path = $this->getServer('PHP_SELF');
			$segs = explode('/', trim($this->getServer('SCRIPT_FILENAME'), '/'));
			$segs = array_reverse($segs);
			$index = 0;
			$last = count($segs);
			$baseUrl = '';
			do {
				$seg = $segs[$index];
				$baseUrl = '/' . $seg . $baseUrl;
				++$index;
			} while (($last > $index) && (false !== ($pos = strpos($path, $baseUrl))) && (0 != $pos));
		}
		// check with the request_uri
		$requestUri = $this->getRequestUri();
		if (0 === strpos($requestUri, $baseUrl)) {
			$this->setBaseUrl($baseUrl);
			return ;
		}
		if (0 === strpos($requestUri, dirname($baseUrl))) {
			$this->setBaseUrl(dirname($baseUrl));
			return ;
		}
		if (!strpos($requestUri, basename($baseUrl))) {
			$this->setBaseUrl('');
			return ;
		}

		// If using mod_rewrite or ISAPI_Rewrite strip the script filename out of baseUrl. $pos !== 0 makes sure it is not matching a value from PATH_INFO or QUERY_STRING
		if ((strlen($requestUri) >= strlen($baseUrl)) && ((false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0))) {
			$baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
		}
		
		$this->setBaseUrl($baseUrl);
	}

	/**
	 * 初始化请求的基本路径(排除脚本名)
	 *
	 * @return $this
	 */
	private function initBasePath() {
		$baseUrl = $this->getBaseUrl();
		if (empty($baseUrl)) {
			$this->_basePath = '';
			return $this;
		}
		$filename = basename($_SERVER['SCRIPT_FILENAME']);
		$basePath = basename($baseUrl) === $filename ? dirname($baseUrl) : $baseUrl;

		$this->_basePath = rtrim($basePath, '/');
		return $this;
    }

	/**
	 * 初始化请求的相对路径(相对于BaseUrl)
	 *
	 * @return $this
	 */
    private function initPathInfo() {
		$baseUrl = $this->getBaseUrl();

		if (null === ($requestUri = $this->getRequestUri())) return $this;
		if ($pos = strpos($requestUri, '?')) $requestUri = substr($requestUri, 0, $pos);

		if ((null !== $baseUrl) && (false === ($pathInfo = substr($requestUri, strlen($baseUrl))))) {
			$pathInfo = '';
		} elseif (null === $baseUrl) {
			$pathInfo = $requestUri;
		}

		$this->_pathInfo = (string) $pathInfo;
		return $this;
	}
    
}

