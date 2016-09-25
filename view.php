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
 * @author Valery Fremaux (valery.fremaux@gmail.com)
 * @licence http://www.gnu.org/copyleft/gpl.html GNU Public Licence
 * @package mod_wowslider
 * @category mod
 */
require('../../config.php');
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
$PAGE->set_url($url);
$context = context_module::instance($cm->id);

$instance = new WowSlider($wowslider, $cm);

$instance->require_js();
$instance->require_css();

// Security.

require_login($course->id);

// Trigger module viewed event.
$event = \mod_wowslider\event\wowslider_viewed::create(array(
    'objectid' => $cm->id,
    'context' => $context,
    'other' => array(
        'objectname' => $wowslider->name
    )
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('wowslider', $wowslider);
$event->trigger();

// Print the page header.

$strwowsliders = get_string('modulenameplural', 'wowslider');
$strwowslider  = get_string('modulename', 'wowslider');
$PAGE->set_title(format_string($wowslider->name));
$PAGE->set_heading('');
$PAGE->navbar->add(get_string('modulename', 'wowslider').': '.$wowslider->name);
$PAGE->set_focuscontrol('');
$PAGE->set_cacheable(true);
$PAGE->set_button(update_module_button($cm->id, $course->id, $strwowslider));

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

echo $OUTPUT->header();

$instance->instance = $id;

$renderer = $PAGE->get_renderer('wowslider');

if (has_capability('mod/wowslider:edit', $context)) {
    $slidesurl = new moodle_url('/mod/wowslider/slides.php', array('id' => $cm->id));
    echo '<div id="wowslider-slides-edit"><a href="'.$slidesurl.'" >'.get_string('editslides', 'wowslider').'</a></div>';
}

echo $renderer->print_body($instance);

if ($course->format == 'page') {
    include_once($CFG->dirroot.'/course/format/page/xlib.php');
    page_print_page_format_navigation($cm, $backtocourse = false);
} else {
    if ($COURSE->format != 'singleactivity') {
        echo '<div style="text-align:center;margin:8px">';
        echo $OUTPUT->single_button(new moodle_url('/course/view.php', array('id' => $course->id)), get_string('backtocourse', 'tracker'), 'post', array('class' => 'backtocourse'));
        echo '</div>';
    }
}

// Finish the page.

echo $OUTPUT->footer($course);

// End of mod/wowslider/view.php
