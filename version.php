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
 * Version details.
 *
 * @author      Valery Fremaux (valery.fremaux@gmail.com)
 * @licence     http://www.gnu.org/copyleft/gpl.html GNU Public Licence
 * @package     mod_wowslider
 * @category    mod
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version  = 2016052302;  // The current module version (Date: YYYYMMDDXX).
$plugin->requires = 2018112800;
$plugin->component = 'mod_wowslider';
$plugin->release = 'Moodle 3.6.0 (Build 2016052302)';
$plugin->maturity = MATURITY_STABLE;

// Non moodle attributes.
$plugin->codeincrement = '3.6.0001';