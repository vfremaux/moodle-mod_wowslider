<?php
// This file is part of the mplayer plugin for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

/**
 * @author Valery Fremaux (valery.fremaux@gmail.com)
 * @package mod_wowslider
 * @category mod
 * @licence http://www.gnu.org/copyleft/gpl.html GNU Public Licence
 */

class mod_wowslider_renderer extends plugin_renderer_base {

    function print_body($wowslider) {
        if (!is_object($wowslider)) {
            return '';
        }
        return $wowslider->print_body();
    }
}