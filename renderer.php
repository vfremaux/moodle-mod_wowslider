<?php

class mod_wowslider_renderer extends plugin_renderer_base {

    function print_body($wowslider) {
        global $CFG, $OUTPUT;

        return $wowslider->print_body();
    }
}