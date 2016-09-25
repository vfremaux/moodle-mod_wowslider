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
$url = new moodle_url('/mod/wowslider/view.php', array('id' => $cm->id));
$context = context_module::instance($cm->id);

$PAGE->set_url($url);

require_login($course, true, $cm);
require_capability('mod/wowslider:edit', $context);

$PAGE->set_heading(get_string('wowslides', 'wowslider'));
$PAGE->navbar->add(get_string('wowslides', 'wowslider'));

$slides = $DB->get_records('wowslider_slide', array('wowsliderid' => $wowslider->id));

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('wowslides', 'wowslider'));

$strfilename = get_string('filename', 'wowslider');
$strurl = get_string('url', 'wowslider');
$strtitle = get_string('title', 'wowslider');
$strurlvideo = get_string('url_video', 'wowslider');

if (!empty($slides)) {
    $table = new html_table();
    $table->head = array($strfilename, $strurl, $strtitle, $strurlvideo, '');
    $table->size = array('15%', '30%', '25%', '25%', '5%');
    $table->align = array('left', 'left', 'left', 'left', 'right');

    foreach ($slides as $slide) {
        $editurl = new moodle_url('/mod/wowslider/edit_slide.php', array('id' => $cm->id, 'slideid' => $slide->id));
        $commands = '<a href="'.$editurl.'"><img src="'.$OUTPUT->pix_url('t/edit').'" /></a>';
        $table->data[] = array($slide->filename, $slide->url, $slide->title, $slide->video, $commands);
    }

    echo html_writer::table($table);
} else {
    echo $OUTPUT->box(get_string('noslides', 'wowslider'));
}

echo $OUTPUT->single_button(new moodle_url('/mod/wowslider/view.php', array('id' => $cm->id)), get_string('back', 'wowslider'));

echo $OUTPUT->footer();
