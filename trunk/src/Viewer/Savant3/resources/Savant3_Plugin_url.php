<?php


class Savant3_Plugin_url extends Savant3_Plugin {

	public function url(array $urlOptions = array(), $queryParams = array(), $name = null, $reset = false, $encode = true) {
		return $this->getFrontController()->getRouter()->assemble($urlOptions, $queryParams, $name, $reset, $encode);
    }
	
	private function getFrontController() {
		return jfFrontController::getInstance();
	}
	
}


