<?php

class ErrorController extends jfAction {

	
	public function errorAction() {
		$exs = $this->getResponse()->getException();
		$this->view->assign('message', $exs[0]->getMessage());
	}

}

