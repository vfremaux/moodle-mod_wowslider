<?php

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

        $this->add_action_buttons();
    }

    function validation($data, $files) {
        return array();
    }
}