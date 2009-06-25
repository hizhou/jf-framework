<?php
require_once 'JFramework/Controller/Router/jfRouterBase.php';

class jfRouter extends jfRouterBase {
	protected $_useDefaultRoutes = true;
	protected $_routes = array();
	protected $_currentRoute = null;
	
	public function addDefaultRoutes() {
		if (!$this->hasRoute('default')) {
			require_once 'JFramework/Controller/Router/Matcher/jfRouteMatcherQueryImpl.php';
			$routeMatcher = new jfRouteMatcherQueryImpl();

			$this->addRoute('default', $routeMatcher);
		}
	}
	
	public function addRoute($name, $route) {
		$this->_routes[$name] = $route;
		return $this;
    }
    
	public function addRoutes($routes) {
		foreach ($routes as $name => $route) {
			$this->addRoute($name, $route);
		}
		return $this;
	}
	
	public function removeRoute($name) {
		if (!isset($this->_routes[$name])) {
			return $this;
		}
		unset($this->_routes[$name]);
		return $this;
	}
	
	public function hasRoute($name) {
		return isset($this->_routes[$name]);
	}
	
	public function getRoute($name) {
		if (!isset($this->_routes[$name])) {
			throw new Exception("Route $name is not defined");
		}
		return $this->_routes[$name];
	}
	
	public function route() {
		if ($this->_useDefaultRoutes) {
			$this->addDefaultRoutes();
		}

		// Find the matching route
        foreach (array_reverse($this->_routes) as $name => $route) {
        	$params = $route->match();
			if ($params) {
				$this->getFrontController()->getRequest()->setParams($params);
				$this->_currentRoute = $name;
				break;
			}
		}
	}
	
	public function getCurrentRouteName() {
		return $this->_currentRoute;
	}
    
	public function assemble($userParams, $queryParams = array(), $name = null, $reset = false, $encode = true) {
		if ($name == null) {
			try {
				$name = $this->getCurrentRouteName();
			} catch (Exception $e) {
				$name = 'default';
			}
		}

		$route = $this->getRoute($name);
		$url = $route->assemble($userParams, $queryParams, $reset, $encode);

		if (!preg_match('|^[a-z]+://|', $url)) {
			$url = rtrim($this->getFrontController()->getBaseUrl(), '/') . '/' . $url;
		}

		return $url;
	}
        
}
