<?php

class mod_wowslider_renderer extends plugin_renderer_base {

    function print_body($wowslider) {
        if (!is_object($wowslider)) {
            return '';
        }
        return $wowslider->print_body();
    }
}