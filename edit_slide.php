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
 * This page prints a particular instance of wowslider
 *
 * @author Matt Bury - matbury@gmail.com
 * @version $Id: view.php,v 1.1 2010/01/15 matbury Exp $
 * @licence http://www.gnu.org/copyleft/gpl.html GNU Public Licence
 * @package wowslider
 */
require_once('../../config.php');
require_once($CFG->dirroot.'/mod/wowslider/lib.php');
require_once($CFG->dirroot.'/mod/wowslider/locallib.php');
require_once($CFG->dirroot.'/mod/wowslider/slide_form.php');

$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
$w  = optional_param('w', 0, PARAM_INT);  // wowslider ID

if ($id) {
    if (!$cm = $DB->get_record('course_modules', array('id' => $id))) {
        print_error('invalidcoursemodule');
    }
    if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }
    if (!$wowslider = $DB->get_record('wowslider', array('id' => $cm->instance))) {
        print_error('invalidwowsliderid', 'wowslider');
    }
} else {
    if (!$wowslider = $DB->get_record('wowslider', array('id' => $w))) {
        print_error('invalidwowsliderid', 'wowslider');
    }
    if (!$course = $DB->get_record('course', array('id' => $wowslider->course))) {
        print_error('coursemisconf');
    }
    if (!$cm = get_coursemodule_from_instance('wowslider', $wowslider->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
}
$url = new moodle_url('/mod/wowslider/edit_slide.php', array('id' => $cm->id));
$context = context_module::instance($cm->id);

$PAGE->set_url($url);

require_login($course, true, $cm);
require_capability('mod/wowslider:edit', $context);

$PAGE->set_heading(get_string('wowslides', 'wowslider'));
$PAGE->set_context($context);

$slides = $DB->get_records('wowslider_slide', array('id' => $wowslider->id));

$slideid = required_param('slideid', PARAM_INT);
$slide = $DB->get_record('wowslider_slide', array('id' => $slideid));

$form = new SlideForm($url, array('filename' => $slide->filename, 'slideid' => $slideid));

if ($form->is_cancelled()) {
    redirect(new moodle_url('/mod/wowslider/slides.php', array('id' => $cm->id)));
}

if ($data = $form->get_data()) {
    $data->id = $data->slideid;
    unset($data->slideid);
    $DB->update_record('wowslider_slide', $data);
    redirect(new moodle_url('/mod/wowslider/slides.php', array('id' => $cm->id)));
}

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('editslide', 'wowslider'));

$form->set_data($slide);
$form->display();

echo $OUTPUT->footer();