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
 * @copyright  2009 Valery Fremaux (http://www.mylearningfactory.com)
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
    $key = 'wowslider/maxallowedfiles';
    $label = get_string('configmaxallowedfiles', 'wowslider');
    $desc = get_string('configmaxallowedfiles_desc', 'wowslider');
    $default = 100;
    $settings->add(new admin_setting_configselect($key, $label, $desc, $default, $maxfilesopts));

    $skinoptions = array(
        0 => get_string('default', 'wowslider'),
        'glass' => get_string('glass', 'wowslider'),
        'transparent' => get_string('transparent', 'wowslider'),
        'gentle' => get_string('gentle', 'wowslider'),
        'twist' => get_string('twist', 'wowslider'),
    );
    $key = 'wowslider/defaultskin';
    $label = get_string('configdefaultskin', 'wowslider');
    $desc = get_string('configdefaultskin_desc', 'wowslider');
    $default = 0;
    $settings->add(new admin_setting_configselect($key, $label, $desc, $default, $skinoptions));

    $effectoptions = array(
        0 => get_string('noeffect', 'wowslider'),
        'glassparallax' => get_string('glassparallax', 'wowslider'),
        'cube' => get_string('cube', 'wowslider'),
        'blur' => get_string('blur', 'wowslider'),
        'rotate' => get_string('rotate', 'wowslider'),
    );
    $key = 'wowslider/defaulteffect';
    $label = get_string('configdefaulteffect', 'wowslider');
    $desc = get_string('configdefaulteffect_desc', 'wowslider');
    $default = 0;
    $settings->add(new admin_setting_configselect($key, $label, $desc, $default, $effectoptions));

    $sql = "
        SELECT
            w.id,
            CONCAT('[', c.shortname, '] ', w.name) as name
        FROM
            {wowslider} w,
            {course} c
        WHERE
            c.id = w.course
        ORDER BY
           name
     ";
    $instancesoptions = $DB->get_records_sql_menu($sql, array());
    if (!empty($instancesoptions)) {
        $key = 'wowslider/localmywowsliderid';
        $label = get_string('configlocalmywowsliderid', 'wowslider');
        $desc = get_string('configlocalmywowsliderid_desc', 'wowslider');
        $settings->add(new admin_setting_configselect($key, $label, $desc, 0, $instancesoptions));
    }
}
