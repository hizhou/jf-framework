<?php



class IndexController extends jfAction {
	
	public function indexAction() {
		$this->view->assign("msg", "Hello World!");
	}


}

