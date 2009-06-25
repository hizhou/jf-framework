<?php

require_once 'JFramework/Controller/jfFrontController.php';

class jfRouteMatcherBase {
	protected function getFrontController() {
		return jfFrontController::getInstance();
	}
    
	protected function generateQueryString($arr) {
		$newArr = array();
		foreach ($arr as $k => $v) {
			$newArr[] = $k . '=' . $v;
		}
		return implode("&", $newArr);
	}
}
