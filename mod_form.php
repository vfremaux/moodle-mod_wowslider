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
 * This view allows checking deck states
 *
 * @package mod_wowslider
 * @category mod
 * @author Valery Fremaux
 * @contributors Etienne Roze
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
* overrides moodleform for test setup
*/
class mod_wowslider_mod_form extends moodleform_mod {

    public function definition() {
        global $CFG, $COURSE;

        $mform    =& $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        $mform->setType('name', PARAM_CLEANHTML);
        $mform->addRule('name', null, 'required', null, 'client');

        $this->add_intro_editor(true, get_string('intro', 'magtest'));

        // mplayerfile
        $mform->addElement('filemanager', 'wowslides', get_string('wowslides', 'wowslider'), null, array('courseid' => $COURSE->id, 'maxfiles' => 30));
        $mform->addRule('wowslides', get_string('required'), 'required', null, 'client');

        $mform->addElement('filemanager', 'tooltips', get_string('tooltips', 'wowslider'), null, array('courseid' => $COURSE->id, 'maxfiles' => 30));

        $mform->addElement('text', 'width', get_string('width', 'wowslider'));
        $mform->setType('width', PARAM_TEXT);

        $mform->addElement('text', 'height', get_string('height', 'wowslider'));
        $mform->setType('height', PARAM_INT);

        $effectoptions = array(
            0 => get_string('noeffect', 'wowslider'),
            'glassparallax' => get_string('glassparallax', 'wowslider'),
            'cube' => get_string('cube', 'wowslider'),
            'blur' => get_string('blur', 'wowslider'),
        );
        $mform->addElement('select', 'effect', get_string('effect', 'wowslider'), $effectoptions);

        $skinoptions = array(
            0 => get_string('default', 'wowslider'),
            'glass' => get_string('glass', 'wowslider'),
            'transparent' => get_string('transparent', 'wowslider'),
            'gentle' => get_string('gentle', 'wowslider'),
        );
        $mform->addElement('select', 'skin', get_string('skin', 'wowslider'), $skinoptions);

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    public function validation($data, $files = null) {
        $errors = array();
        return $errors;
    }

    public function set_data($data) {

        if ($data->coursemodule) {
            $context = context_module::instance($data->coursemodule);

            $maxbytes = -1;

            // Saves draft customization image files into definitive filearea. (prepared for extension)
            $instancefiles = wowslider_get_fileareas();
            foreach ($instancefiles as $if) {
                $draftitemid = file_get_submitted_draft_itemid($if);
                $maxfiles = ($if == 'wowslides') ? 30 : 1;
                file_prepare_draft_area($draftitemid, $context->id, 'mod_wowslider', $if, 0, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => $maxfiles));
                $data->$if = $draftitemid;
            }
        }
        parent::set_data($data);
    }
}
