<?php


class Savant3_Plugin_formRadio extends Savant3_Plugin {

	public function formRadio($name, $value = null, $options = array(), $attrs = "", $listsep = "<br />\n") {
		$html = "";
		foreach ($options as $optionKey => $optionStr) {
			$html .= '<label style="white-space: nowrap;">';
			$checked = null !== $value && $value == $optionKey ? 'checked' : '';
			$html .= '<input type="radio" name="' . $name . '" value="'. $optionKey .'" '. $checked .' '.$attrs.'>' . $optionStr;
			$html .= '</label>';
			$html .= $listsep;
		}
		return substr($html, 0, - strlen($listsep));
	}
	
}


