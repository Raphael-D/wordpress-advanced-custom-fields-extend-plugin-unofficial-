<?php

namespace WTS_EAE\Modules\ComparisonTable;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'ComparisonTable',
		];
	}

	public function get_name() {
		return 'eae-comparisontable';
	}

	public function get_title() {

		return __('Comparison Table', 'wts-eae');

	}

}