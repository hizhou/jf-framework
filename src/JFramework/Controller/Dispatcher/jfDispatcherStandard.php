<?php
require_once 'JFramework/Controller/Dispatcher/jfDispatcherBase.php';

class jfDispatcherStandard extends jfDispatcherBase {
	protected $_curDirectory;
	protected $_curModule;
	protected $_controllerDirectory = array();

	
	public function addControllerDirectory($path, $module = null) {
		if (null === $module) $module = $this->_defaultModule;

		$module = (string) $module;
		$path = rtrim((string) $path, '/\\');

		$this->_controllerDirectory[$module] = $path;
		return $this;
	}
	public function getControllerDirectory($module = null) {
		if (null === $module) return $this->_controllerDirectory;

		$module = (string) $module;
		return array_key_exists($module, $this->_controllerDirectory) ? $this->_controllerDirectory[$module] : null;
	}
	
	public function dispatch() {
		$this->initCurrConfig();
		$className = $this->getControllerClass();
		if (!$this->isDispatchable($className)) {
			throw new Exception('Invalid controller specified (' . $this->getFrontController()->getRequest()->getControllerName() . ')');
			//$className = $this->getDefaultControllerClass();
		}

		//Load the controller class file
		$className = $this->loadClass($className);
		$controller = new $className($this->getFrontController()->getRequest(), $this->getFrontController()->getResponse());

		$action = $this->getActionMethod($this->getFrontController()->getRequest());

		$obLevel = ob_get_level();
		ob_start();
		
		try {
			$controller->dispatch($action);
		} catch (Exception $e) {
			// Clean output buffer on error
			$curObLevel = ob_get_level();
			if ($curObLevel > $obLevel) {
 				do {
					ob_get_clean();
					$curObLevel = ob_get_level();
				} while ($curObLevel > $obLevel);
			}
			throw $e;
		}

		$content = ob_get_clean();
		$this->getFrontController()->getResponse()->appendBody($content);

		$controller = null;
	}
	
	public function dispatchError() {
		//TODO not a good way
		$this->_curModule = $this->_defaultModule;
		$this->_curDirectory = $this->getControllerDirectory($this->_defaultModule);
		$this->getFrontController()->getRequest()->setActionName('error');
		$this->getFrontController()->getRequest()->setControllerName('error');
		$this->getFrontController()->getRequest()->setModuleName('deafult');
		$this->getFrontController()->getRequest()->setParams(array('module'=>'deafult', 'controller'=>'error','action'=>'error'));

		$this->dispatch();
	}

	public function isValidModule($module) {
        if (!is_string($module)) return false;

		$module = strtolower($module);
		$controllerDir = $this->getControllerDirectory();
		foreach (array_keys($controllerDir) as $moduleName) {
			if ($module == strtolower($moduleName)) return true;
		}
		return false;
	}

    public function getDispatchDirectory() {
		return $this->_curDirectory;
	}

	
	private function getActionMethod() {
		$action = $this->getFrontController()->getRequest()->getActionName();
		if (empty($action)) {
			$action = $this->getDefaultAction();
			$this->getFrontController()->getRequest()->setActionName($action);
		}

		return $this->formatActionName($action);
	}
	
 	private function isDispatchable($className) {
		return $this->checkClassExist($className) ? true : false;
	}
	
	private function loadClass($className) {
		if (null !== ($loadFilePath = $this->checkClassExist($className))) {
			include $loadFilePath;
		}
		if (!class_exists($className, false)) throw new Exception('Invalid controller class ("' . $className . '")');
		return $className;
	}

	private function checkClassExist($className) {
		if (!$className) return false;
		if (class_exists($className, false)) return true;
		$loadFilePath = $this->getDispatchDirectory() . DIRECTORY_SEPARATOR . $this->classToFilename($className);
		return file_exists($loadFilePath) ? $loadFilePath : null;
	}

	private function getControllerClass() {
		$controllerName = $this->getFrontController()->getRequest()->getControllerName();
		if (empty($controllerName)) return $this->getDefaultControllerClass();
		$className = $this->formatControllerName($controllerName);

		return $className;
	}

	private function getDefaultControllerClass() {
		$controllerName = $this->getDefaultControllerName();
		$default = $this->formatControllerName($controllerName);
		$this->getFrontController()->getRequest()->setControllerName($controllerName)
			->setActionName(null);
		
		return $default;
	}
	
	private function initCurrConfig() {
		$controllerDirs = $this->getControllerDirectory();
		$moduleName = $this->getFrontController()->getRequest()->getModuleName();
		if ($moduleName && $this->isValidModule($moduleName)) {
			$this->_curModule = $moduleName;
			$this->_curDirectory = $controllerDirs[$moduleName];
		} elseif ($this->isValidModule($this->_defaultModule)) {
			$this->getFrontController()->getRequest()->setModuleName($this->_defaultModule);
			$this->_curModule = $this->_defaultModule;
			$this->_curDirectory = $controllerDirs[$this->_defaultModule];
		} else {
			throw new Exception('No default module defined for this application');
		}
	}

	private function classToFilename($class) {
		return $class . '.php';
	}

}

