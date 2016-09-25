<?php
// This file is part of Moodle - http://moodle.org/
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

/**
 * The wowslider module configuration variables
 *
 * The values defined here are often used as defaults for all module instances.
 *
 * @package    mod
 * @subpackage wowslider
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $maxfilesopts = array('-1' => get_string('unlimited', 'wowslider'),
                          '10' => '10 '.get_string('slides', 'wowslider'),
                          '50' => '50 '.get_string('slides', 'wowslider'),
                          '100' => '100 '.get_string('slides', 'wowslider'),
                          '200' => '200 '.get_string('slides', 'wowslider')
                          );
    $settings->add(new admin_setting_configselect('wowslider/maxallowedfiles', get_string('maxallowedfiles', 'wowslider'),
                        get_string('configmaxallowedfiles', 'wowslider'), 100, $maxfilesopts));
}
