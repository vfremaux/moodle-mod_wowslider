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
 * @package mod-wowslider
 * @category mod
 **/
require_once("../../config.php");
require_once('lib.php');

$id = required_param('id', PARAM_INT);   // Course ID.

if (! $course = $DB->get_record('course', array('id' => $id))) {
    print_error('coursemisconf');
}

require_login($course->id);
add_to_log($course->id, 'wowslider', "view all", "index.php?id=$course->id", "");

// Get all required strings.
$strwowsliders = get_string('modulenameplural', 'wowslider');
$strwowslider  = get_string('modulename', 'wowslider');

// Print the header.
if ($course->category) {
    $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
} else {
    $navigation = '';
}

$PAGE->set_title("$course->shortname: $strwowsliders");
$PAGE->set_heading("$course->fullname");
$PAGE->set_focuscontrol("");
$PAGE->set_cacheable(true);
$PAGE->set_button("");

echo $OUTPUT->header();

// Get all the appropriate data.

if (! $sliders = get_all_instances_in_course('wowslider', $course)) {
    notice("There are no slides", "../../course/view.php?id=$course->id");
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
