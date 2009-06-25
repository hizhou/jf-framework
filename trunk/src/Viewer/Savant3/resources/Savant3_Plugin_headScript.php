<?php


class Savant3_Plugin_headScript extends Savant3_Plugin {

	private $items = array();

	public function headScript() {
		
		return $this;
	}
	
	public function appendFile($uri, $fileType) {
		$this->items[] = '<script type="'.$fileType.'" src="'.$uri.'"></script>';
	}
	
	public function __toString() {
		return implode("\r\n", $this->items);
	}
	
}


