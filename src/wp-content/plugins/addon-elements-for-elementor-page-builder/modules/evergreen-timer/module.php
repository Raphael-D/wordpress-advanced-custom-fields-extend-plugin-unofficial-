<?php

namespace WTS_EAE\Modules\EvergreenTimer;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'Evergreen_Timer',
		];
	}

	public function get_name() {
		return 'eae-evergreen-timer';
	}
}