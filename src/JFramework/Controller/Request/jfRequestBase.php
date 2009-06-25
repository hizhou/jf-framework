<?php

class jfRequestBase {

	protected $_module;
	protected $_moduleKey = 'module';
	protected $_controller;
	protected $_controllerKey = 'controller';
	protected $_action;
	protected $_actionKey = 'action';
	protected $_params = array();
	
	public function getModuleName() {
		if (null === $this->_module) {
			$this->_module = $this->getParam($this->getModuleKey());
		}

		return $this->_module;
	}
	public function setModuleName($value) {
		$this->_module = $value;
		return $this;
	}
	
	public function getControllerName() {
		if (null === $this->_controller) {
			$this->_controller = $this->getParam($this->getControllerKey());
		}

		return $this->_controller;
	}
	public function setControllerName($value) {
		$this->_controller = $value;
		return $this;
	}
	
	public function getActionName() {
		if (null === $this->_action) {
			$this->_action = $this->getParam($this->getActionKey());
		}

		return $this->_action;
	}
	public function setActionName($value) {
		$this->_action = $value;
		return $this;
	}


	public function getModuleKey() {
		return $this->_moduleKey;
	}
	public function setModuleKey($key) {
		$this->_moduleKey = (string) $key;
		return $this;
	}

	public function getControllerKey() {
		return $this->_controllerKey;
	}
	public function setControllerKey($key) {
		$this->_controllerKey = (string) $key;
		return $this;
	}

	public function getActionKey() {
		return $this->_actionKey;
	}
	public function setActionKey($key) {
		$this->_actionKey = (string) $key;
		return $this;
	}
	
	public function getParam($key, $default = null) {
		$key = (string) $key;
		if (isset($this->_params[$key])) {
			return $this->_params[$key];
		}

		return $default;
	}
	public function setParam($key, $value) {
		$key = (string) $key;

		if ((null === $value) && isset($this->_params[$key])) {
			unset($this->_params[$key]);
		} elseif (null !== $value) {
			$this->_params[$key] = $value;
		}

		return $this;
	}
	public function getParams() {
		return $this->_params;
	}
	public function setParams(array $array) {
		$this->_params = $this->_params + (array) $array;

		foreach ($this->_params as $key => $value) {
			if (null === $value) {
				unset($this->_params[$key]);
			}
		}

		return $this;
	}
}


