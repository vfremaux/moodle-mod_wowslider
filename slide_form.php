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
require_once($CFG->dirroot.'/lib/formslib.php');

class SlideForm extends moodleform {

    function definition() {

        $mform = $this->_form;

        $mform->addElement('header', 'h1', get_string('slidedata', 'wowslider'));

        $mform->addElement('hidden', 'slideid', $this->_customdata['slideid']);
        $mform->setType('slideid', PARAM_INT);

        $mform->addElement('static', 'filename', get_string('filename', 'wowslider'));
        $mform->setDefault('filename', $this->_customdata['filename']);

        $mform->addElement('text', 'url', get_string('url', 'wowslider'));
        $mform->setType('url', PARAM_URL);

        $mform->addElement('text', 'title', get_string('title', 'wowslider'));
        $mform->setType('title', PARAM_TEXT);

        $mform->addElement('text', 'tooltip', get_string('tooltip', 'wowslider'));
        $mform->setType('tooltip', PARAM_TEXT);

        $mform->addElement('text', 'video', get_string('url_video', 'wowslider'));
        $mform->setType('video', PARAM_TEXT);

        $this->add_action_buttons();
    }

    function validation($data, $files) {
        return array();
    }
}