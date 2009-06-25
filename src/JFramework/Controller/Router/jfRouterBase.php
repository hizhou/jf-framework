<?php

require_once 'JFramework/Controller/jfFrontController.php';
		
class jfRouterBase {
	
	public function getFrontController() {
		return jfFrontController::getInstance();
	}
}
