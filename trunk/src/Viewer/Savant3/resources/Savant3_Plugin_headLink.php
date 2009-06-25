<?php


class Savant3_Plugin_headLink extends Savant3_Plugin {

	private $items = array();

	public function headLink() {
		
		return $this;
	}
	
	public function appendStylesheet($uri) {
		$this->items[] = '<link rel="stylesheet" type="text/css" href="'.$uri.'" />';
	}
	
	public function __toString() {
		return implode("\r\n", $this->items);
	}
	
}


