<?php

namespace WTS_EAE\Modules\DualButton;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

    public function get_widgets() {
        return [
            'DualButton',
        ];
    }

    public function get_name() {
        return 'eae-dualbutton';
    }

}