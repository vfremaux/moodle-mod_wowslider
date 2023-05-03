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

require('../../../config.php');
require_once($CFG->dirroot.'/mod/wowslider/lib.php');
require_once($CFG->dirroot.'/mod/wowslider/locallib.php');

$wsid = required_param('wsid', PARAM_INT); // Slider instance id.
$action = required_param('what', PARAM_TEXT);

if (!$wowslider = $DB->get_record('wowslider', array('id' => $wsid))) {
    die;
}
if (!$cm = get_coursemodule_from_instance('wowslider', $wsid)) {
    die;
}
if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    die;
}

if (!$wowslider->notificationslide) die;

$context = context_module::instance($cm->id);

$url = new moodle_url('/mod/wowslider/ajax/services.php');

$PAGE->set_url($url);

// Security.

require_login($course, true, $cm);

$renderer = $PAGE->get_renderer('wowslider');

// Make a record for user anyhow.
if (!$wsuserdata = $DB->get_record('wowslider_slide_view', array('userid' => $USER->id, 'wowsliderid' => $wsid))) {
    $wsuserdata = new StdClass();
    $wsuserdata->userid = $USER->id;
    $wsuserdata->wowsliderid = $wsid;
    $wsuserdata->views = 1;
    $wsuserdata->timefirstview = time();
    $wsuserdata->id = $DB->insert_record('wowslider_slide_view', $wsuserdata);
}

if ($action == 'complete') {

    $wsuserdata->timecomplete = time();

    $DB->update_record('wowslider_slide_view', $wsuserdata);
    // Mark completed on mediaviewed criteria.
    $completion = new completion_info($course);
    if ($completion->is_enabled($cm) && $wowslider->completionmediaviewed) {
        $completion->update_state($cm, COMPLETION_COMPLETE);
        echo get_string('completed', 'wowslider');
    }
} else {
    die('Invalid action');
}
