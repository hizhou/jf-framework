<?php


class Savant3_Plugin_status extends Savant3_Plugin {

	private $setting = array(
		'0' => array(
			'text' => '��Ч',
			'color' => 'red',
			'lt' => false,
		),
		'1' => array(
			'text' => '��Ч',
			'color' => 'green',
			'lt' => false,
		),
		'2' => array(
			'text' => '��ֹ',
			'color' => 'gray',
			'lt' => false,
		),
		'3' => array(
			'text' => '���',
			'color' => 'gray',
			'lt' => true,
		),
	);
	
	public function status($status, $setting = null) {
		if (null == $setting) $setting = $this->setting;
		return '<font color="' . $setting[$status]['color'] . '">'
			. ($setting[$status]['lt'] ? '<s>' : '')
			. $setting[$status]['text']
			. ($setting[$status]['lt'] ? '</s>' : '')
			. '</font>';
    }

	
}


