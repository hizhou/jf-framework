<?php

class jfAction {
	
	protected $_request = null;
	protected $_response = null;
	
	public $viewSuffix = 'phtml';
	public $view;
	
	public function __construct($request, $response) {
		$this->initView();
		$this->setRequest($request)
			->setResponse($response)
			->init();
	}
	
	public function init() {
		
	}
	
	public function initView() {
		require_once 'Viewer/Savant3.php';
		$this->view = new Savant3(array('exceptions'=>true));
		//TODO set viewer path
		$this->view->setPath('template', dirname($this->getFrontController()->getDispatcher()->getDispatchDirectory()) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'scripts');
		return $this->view;
	}
    
	public function render($action = null, $name = null, $noController = false) {
		//$view = $this->initView();
		$script = $this->getViewScript($action, $noController);

		$this->getResponse()->appendBody(
			$this->view->display($script),
			$name
		);
	}
	
	public function getViewScript($action = null, $noController = null) {
		$request = $this->getRequest();
		
		if (null === $action) $action = $this->getRequest()->getActionName();
		$script = $action . '.' . $this->viewSuffix;

		if (!$noController) {
			$controller = $request->getControllerName();
			$script = $controller . DIRECTORY_SEPARATOR . $script;
		}

		return $script;
    }
    
	public function preDispatch() {
		
	}
	
	public function postDispatch() {
		
	}
	
	public function dispatch($action) {
		$this->preDispatch();
		
		//if ($this->getRequest()->isDispatched()) {
		if (in_array($action, get_class_methods($this))) {
			$this->$action();
		} else {
			$this->__call($action, array());
		}
		$this->postDispatch();
		$this->render();
    }
    
	public function getRequest() {
		return $this->_request;
	}
	public function setRequest($request) {
		$this->_request = $request;
		return $this;
	}

	public function getResponse() {
		return $this->_response;
	}
	public function setResponse($response) {
		$this->_response = $response;
		return $this;
	}
	
	public function __call($methodName, $args) {
		if ('Action' == substr($methodName, -6)) {
			$action = substr($methodName, 0, strlen($methodName) - 6);
 			throw new Exception(sprintf('Action "%s" does not exist and was not trapped in __call()', $action), 404);
		}
		throw new Exception(sprintf('Method "%s" does not exist and was not trapped in __call()', $methodName), 500);
	}
	
	public function getFrontController() {
		return jfFrontController::getInstance();
	}

}

