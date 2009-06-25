<?php
require_once 'JFramework/Controller/Router/Matcher/jfRouteMatcherBase.php';

class jfRouteMatcherDirImpl extends jfRouteMatcherBase {
	const DELIMITER = '/';
	
	protected $_values = array();
	protected $_moduleValid = false;
	
	public function match() {

		$values = array();
		$params = array();
		$path = trim($this->getFrontController()->getRequest()->getPathInfo(), self::DELIMITER);

		if ($path != '') {

			$path = explode(self::DELIMITER, $path);

			if ($this->getFrontController()->getDispatcher()->isValidModule($path[0])) {
				$values[$this->getFrontController()->getRequest()->getModuleKey()] = array_shift($path);
				$this->_moduleValid = true;
			}

			if (count($path) && !empty($path[0])) {
				$values[$this->getFrontController()->getRequest()->getControllerKey()] = array_shift($path);
			}

			if (count($path) && !empty($path[0])) {
				$values[$this->getFrontController()->getRequest()->getActionKey()] = array_shift($path);
			}

			if ($numSegs = count($path)) {
				for ($i = 0; $i < $numSegs; $i = $i + 2) {
					$key = urldecode($path[$i]);
					$val = isset($path[$i + 1]) ? urldecode($path[$i + 1]) : null;
					$params[$key] = $val;
				}
			}
		}

		$this->_values = $values + $params;

		return $this->_values;
	}
	
	public function assemble($data = array(), $queryParams = array(), $reset = false, $encode = true) {
		$params = (!$reset) ? $this->_values : array();

        foreach ($data as $key => $value) {
			if ($value !== null) {
				$params[$key] = $value;
			} elseif (isset($params[$key])) {
				unset($params[$key]);
			}
		}

		$moduleKey = $this->getFrontController()->getRequest()->getModuleKey();
		$controllerKey = $this->getFrontController()->getRequest()->getControllerKey();
		$actionKey = $this->getFrontController()->getRequest()->getActionKey();
		
		$defaults = array(
			$moduleKey => $this->getFrontController()->getDispatcher()->getDefaultModule(),
			$controllerKey => $this->getFrontController()->getDispatcher()->getDefaultControllerName(),
			$actionKey => $this->getFrontController()->getDispatcher()->getDefaultAction(),
		);
		
		$params += $defaults;

		$url = '';
		
		if ($this->_moduleValid || array_key_exists($moduleKey, $data)) {
			if ($params[$moduleKey] != $defaults[$moduleKey]) {
				$module = $params[$moduleKey];
			}
		}
		unset($params[$moduleKey]);

		$controller = $params[$controllerKey];
		unset($params[$controllerKey]);

		$action = $params[$actionKey];
		unset($params[$actionKey]);

		foreach ($params as $key => $value) {
			if ($encode) $value = urlencode($value);
			$url .= self::DELIMITER . $key;
			$url .= self::DELIMITER . $value;
		}

		if (!empty($url) || $action !== $defaults[$actionKey]) {
			if ($encode) $action = urlencode($action);
			$url = self::DELIMITER . $action . $url;
		}

		if (!empty($url) || $controller !== $defaults[$controllerKey]) {
			if ($encode) $controller = urlencode($controller);
			$url = self::DELIMITER . $controller . $url;
		}

		if (isset($module)) {
			if ($encode) $module = urlencode($module);
			$url = self::DELIMITER . $module . $url;
		}

        $url = ltrim($url, self::DELIMITER);
        $url .= is_array($queryParams) && count($queryParams) ? "?" . $this->generateQueryString($queryParams) : "";
        return $url;
	}
}

