<?php


class Savant3_Plugin_headMeta extends Savant3_Plugin {

	private $items = array();

	public function headMeta() {
		
		return $this;
	}
	
	public function appendName($name, $content) {
		$this->items[] = '<meta name="'.$name.'" content="'.$content.'">';
	}
	
	public function appendHttpEquiv($name, $content) {
		$this->items[] = '<meta http-equiv="'.$name.'" content="'.$content.'">';
	}
	
	public function __toString() {
		return implode("\r\n", $this->items);
	}
	
}


