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
 * This page lists all the instances of wowslider in a particular course
 *
 * @author Valery Fremaux (valery.fremaux@gmail.com)
 * @package mod_wowslider
 * @licence http://www.gnu.org/copyleft/gpl.html GNU Public Licence
 * @category mod
 */
require('../../config.php');
require_once($CFG->dirroot.'/mod/wowslider/lib.php');

$id = required_param('id', PARAM_INT); // Course ID.

if (! $course = $DB->get_record('course', array('id' => $id))) {
    print_error('coursemisconf');
}

// Security.

require_login($course->id);

$context = context_module::instance($cm->id);
// Trigger module viewed event.
$event = \mod_wowslider\event\wowslider_viewedall::create(array(
    'objectid' => $course->id,
    'context' => $context,
    'other' => array(
        'objectname' => 'wowslider'
    )
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->trigger();

// Get all required strings.
$strwowsliders = get_string('modulenameplural', 'wowslider');
$strwowslider  = get_string('modulename', 'wowslider');

$PAGE->set_title("$course->shortname: $strwowsliders");
$PAGE->set_heading("$course->fullname");
$PAGE->set_cacheable(true);

echo $OUTPUT->header();

// Get all the appropriate data.

if (! $sliders = get_all_instances_in_course('wowslider', $course)) {
    echo $OUTPUT->notification(get_string('noslides', 'wowslider'), new moodle_url('/course/view.php', array('id' => $course->id)));
    echo $OUTPUT->footer();
    die;
}

// Print the list of instances (your module will probably extend this).

$timenow = time();
$strname  = get_string('name');
$strweek  = get_string('week');
$strtopic  = get_string('topic');

if ($course->format == 'weeks') {
    $table->head  = array ($strweek, $strname);
    $table->align = array ('center', 'left');
} else if ($course->format == 'topics') {
    $table->head  = array ($strtopic, $strname);
    $table->align = array ('center', 'left', 'left', 'left');
} else {
    $table->head  = array ($strname);
    $table->align = array ('left', 'left', 'left');
}

foreach ($sliders as $slider) {
    $slidername = format_string($slider->name);
    if (!$slider->visible) {
        //Show dimmed if the mod is hidden
        $link = "<a class=\"dimmed\" href=\"view.php?id=$slider->coursemodule\">$slidername</a>";
    } else {
        //Show normal if the mod is visible
        $link = "<a href=\"view.php?id=$slider->coursemodule\">$slider->name</a>";
    }

    if ($course->format == "weeks" or $course->format == "topics") {
        $table->data[] = array ($slider->section, $link);
    } else {
        $table->data[] = array ($link);
    }
}

echo "<br />";

echo html_writer::table($table);

// Finish the page.
echo $OUTPUT->footer($course);
