<?php


class Savant3_Plugin_arrayToJs extends Savant3_Plugin {

	private $separator = "\t";
	private $nl = "\n";
	
	public function arrayToJs($arr, $deep = 0) {
		$indent = str_repeat($this->separator, $deep);
		$newArr = array();
		foreach($arr as $k => $v) {
			$newArr[] = $indent . $this->separator . $k . ": " . (is_array($v) ? $this->arrayToJs($v, $deep+1) : "'$v'");
		}
		return "{" . $this->nl . implode("," . $this->nl, $newArr) . $this->nl . $indent. "}";
	}

}


