<?php

/**
* 
* Plugin to generate a formatted date using strftime() conventions.
* 
* @package Savant3
* 
* @author Paul M. Jones <pmjones@ciaweb.net>
* 
* @license http://www.gnu.org/copyleft/lesser.html LGPL
* 
* @version $Id: Savant3_Plugin_date.php,v 1.3 2005/03/07 14:40:16 pmjones Exp $
*
*/

/**
* 
* Plugin to generate a formatted date using strftime() conventions.
* 
* @package Savant3
* 
* @author Paul M. Jones <pmjones@ciaweb.net>
* 
*/

class Savant3_Plugin_date extends Savant3_Plugin {

	public $default = 'm月d日  H时i分';
	
	public function date($timestamp, $format = null) {		
		if (is_null($format)) {
			$format = $this->default;
		}
		
		return $timestamp ? date($format, $timestamp) : '';
	}

}
