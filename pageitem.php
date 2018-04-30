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

/**
 * @author Valery Fremaux (valery.fremaux@gmail.com)
 * @package mod_wowslider
 * @category mod
 * @licence http://www.gnu.org/copyleft/gpl.html GNU Public Licence
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/wowslider/locallib.php');

/**
 * implements an alternative representation of this activity for the "page"
 * format.
 */
function wowslider_set_instance(&$block) {
    global $DB, $PAGE, $COURSE;

    $str = '';

    $context = context_module::instance($block->cm->id);

    $wowsliderrec = $DB->get_record('wowslider', array('id' => $block->cm->instance));
    $wowslider = new wowslider($wowsliderrec, $block->cm);
    $wowslider->require_js();
    $wowslider->require_css();

    // Transfer content from title to content.
    $block->title = format_string($wowslider->name);

    $renderer = $PAGE->get_renderer('wowslider');
    $str .= $renderer->print_body($wowslider);

    $course = $DB->get_record('course', array('id' => $wowslider->course));
    $completion = new completion_info($course);
    $completion->set_module_viewed($block->cm);

    // Trigger module viewed event.
    $event = \mod_wowslider\event\wowslider_viewed::create(array(
        'objectid' => $block->cm->id,
        'context' => $context,
        'other' => array(
            'objectname' => $wowsliderrec->name
        )
    ));
    $event->add_record_snapshot('course_modules', $block->cm);
    $event->add_record_snapshot('course', $COURSE);
    $event->add_record_snapshot('wowslider', $wowsliderrec);
    $event->trigger();

    $block->content->text = $str;
    return true;
}
