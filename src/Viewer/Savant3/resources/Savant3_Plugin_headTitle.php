<?php


class Savant3_Plugin_headTitle extends Savant3_Plugin {

	private $items = array();
	private $separator = ' - ';
	
	public function headTitle() {
		
		return $this;
	}
	
	public function setSeparator($separator) {
		$this->separator = $separator;
	}
	public function prepend($title) {
		$items = $this->items;
		$items = array_merge(array($title), $items);
		$this->items = $items;
	}
	
	public function __toString() {
		return '<title>' . implode($this->separator, $this->items) . '</title>';
	}
	
}


