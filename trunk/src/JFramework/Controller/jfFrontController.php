<?php

class jfFrontController {
	protected static $_instance = null;
	
	protected $_baseUrl = null;
	protected $_controllerDir = null;
	protected $_moduleControllerDirectoryName = 'controllers';
	
	protected $_request = null;
	protected $_response = null;
	protected $_router = null;
	protected $_dispatcher = null;
	
	protected $_throwExceptions = false;
	
	public static function getInstance() {
		if (null === self::$_instance) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
	
//for Dispatcher module.dir config
	
	public function addModuleDirectory($path) {
		$path = realpath($path);
		if (!is_dir($path)) throw new Exception('module path err!');
		
		$dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if ('.' == $file || '..' == $file || !is_dir($path . DIRECTORY_SEPARATOR. $file)) {
            	continue;
            }
            $module = $file;
			$moduleDir = $path . DIRECTORY_SEPARATOR. $file . DIRECTORY_SEPARATOR . $this->getModuleControllerDirectoryName();
			$this->addControllerDirectory($moduleDir, $module);
        }
        closedir($dh);

		return $this;
	}
	public function addControllerDirectory($directory, $module = null) {
		$this->getDispatcher()->addControllerDirectory($directory, $module);
		return $this;
	}
	public function setModuleControllerDirectoryName($name = 'controllers') {
		$this->_moduleControllerDirectoryName = (string) $name;
		return $this;
	}
	public function getModuleControllerDirectoryName() {
		return $this->_moduleControllerDirectoryName;
	}

// --end

//for Request baseurl config

	public function setBaseUrl($base = null) {
		$this->_baseUrl = $base;
		if ((null !== ($request = $this->getRequest())) && (method_exists($request, 'setBaseUrl'))) {
			$request->setBaseUrl($base);
		}
		return $this;
	}
	public function getBaseUrl() {
		$request = $this->getRequest();
		if ((null !== $request) && method_exists($request, 'getBaseUrl')) {
			return $request->getBaseUrl();
		}
		return $this->_baseUrl;
	}

// --end

	public function setRequest($request) {
		$this->_request = $request;
 		return $this;
	}
	public function getRequest() {
		return $this->_request;
	}
	
	public function setRouter($router) {
		//TODO ??
		//$router->setFrontController($this);
		$this->_router = $router;

		return $this;
	}
	public function getRouter() {
		if (null == $this->_router) $this->initRouter();
		return $this->_router;
	}
    
	public function setDispatcher($dispatcher) {
		$this->_dispatcher = $dispatcher;
		return $this;
	}
	public function getDispatcher() {
		if (null == $this->_dispatcher) $this->initDispatcher();
		return $this->_dispatcher;
	}
    
	public function setResponse($response) {
		$this->_response = $response;
		return $this;
	}
	public function getResponse() {
		return $this->_response;
	}
    
	public function throwExceptions($flag = null) {
		if ($flag !== null) {
			$this->_throwExceptions = (bool) $flag;
			return $this;
		}

		return $this->_throwExceptions;
	}
	
	private function initRequest() {
		require_once 'JFramework/Controller/Request/jfRequestHttp.php';
		$this->setRequest(new jfRequestHttp());

		if (is_callable(array($this->_request, 'setBaseUrl'))) {
			if (null !== $this->_baseUrl) $this->_request->setBaseUrl($this->_baseUrl);
		}
	}
	
	private function initResponse() {
		require_once 'JFramework/Controller/Response/jfResponseHttp.php';
		$this->setResponse(new jfResponseHttp());
	}
	private function initRouter() {
		require_once 'JFramework/Controller/Router/jfRouter.php';
		$this->setRouter(new jfRouter());
	}
	private function initDispatcher() {
		require_once 'JFramework/Controller/Dispatcher/jfDispatcherStandard.php';
		$this->setDispatcher(new jfDispatcherStandard());
	}
	
	public function dispatch() {
		$this->initRequest();
		$this->initResponse();

		$router = $this->getRouter();
        
		$dispatcher = $this->getDispatcher();
        
		//Begin dispatch
		try {
			$router->route();
			$dispatcher->dispatch();
//throw new Exception('a');
		} catch (Exception $e) {
			if ($this->throwExceptions()) {
				throw $e;
			}
			$this->_response->setException($e);
		}

		if ($this->_response->isException()) {
			$dispatcher->dispatchError();
		}

		$this->_response->sendResponse();

	}
}

