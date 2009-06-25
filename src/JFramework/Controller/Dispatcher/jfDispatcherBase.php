<?php
require_once 'JFramework/Controller/jfFrontController.php';

class jfDispatcherBase {
	protected $_defaultAction = 'index';
	protected $_defaultController = 'index';
	protected $_defaultModule = 'default';

	protected $_frontController; //??

	protected $_response = null;

	protected $_pathDelimiter = '_';
	protected $_wordDelimiter = array('-', '.');



	public function formatControllerName($unformatted) {
		return ucfirst($this->_formatName($unformatted)) . 'Controller';
	}
    public function formatActionName($unformatted) {
		$formatted = $this->_formatName($unformatted, true);
		return strtolower(substr($formatted, 0, 1)) . substr($formatted, 1) . 'Action';
    }
	public function formatModuleName($unformatted) {
		if ($this->_defaultModule == $unformatted) return $unformatted;
		return ucfirst($this->_formatName($unformatted));
	}

    //??
	public function getPathDelimiter() {
		return $this->_pathDelimiter;
	}
	public function setPathDelimiter($spec) {
		if (!is_string($spec)) {
			throw new Exception('Invalid path delimiter');
		}
		$this->_pathDelimiter = $spec;
		return $this;
	}

    protected function _formatName($unformatted, $isAction = false) {
        // preserve directories
        //TODO refactor
        $formatted = $unformatted;
        return $formatted;
    }
    
	public function getFrontController() {
		return jfFrontController::getInstance();
	}

	public function getDefaultModule() {
		return $this->_defaultModule;
	}
	public function setDefaultModule($module) {
		$this->_defaultModule = (string) $module;
		return $this;
	}

	public function getDefaultControllerName() {
		return $this->_defaultController;
	}
	public function setDefaultControllerName($controller) {
		$this->_defaultController = (string) $controller;
		return $this;
	}

	public function getDefaultAction() {
		return $this->_defaultAction;
	}
	public function setDefaultAction($action) {
		$this->_defaultAction = (string) $action;
		return $this;
	}
}

