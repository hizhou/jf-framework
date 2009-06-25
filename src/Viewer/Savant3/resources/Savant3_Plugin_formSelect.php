<?php


class Savant3_Plugin_formSelect extends Savant3_Plugin {

	public function formSelect($name, $value = null, $options = array(), $attrs = "", $isempty = "") {
		$html = '<select name="'.$name.'"';
		if ($name == "disabled") {
			$html .= ' disabled';
		}
		$html .= ' '.$attrs.'>';
		if ($isempty != '') {
			$html .= '<option value="">'.$isempty.'</option>';
		}
		foreach ($options as $k => $v) {
			$html .= '<option value="'.$k.'"';
			if (null !== $value && $k == $value) {
				$html .= ' selected="selected"';			
			}
			$html .= '>'.$v.'</option>';
		}
		$html .= '</select>';
		return $html;
	}
	
}


