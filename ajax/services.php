<?php

require('../../../config.php');
require_once($CFG->dirroot.'/mod/wowslider/lib.php');
require_once($CFG->dirroot.'/mod/wowslider/locallib.php');

$wsid = required_param('wsid', PARAM_INT); // Slider instance id
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
    // mark completed on mediaviewed criteria.
    $completion = new completion_info($course);
    if ($completion->is_enabled($cm) && $wowslider->completionmediaviewed) {
        $completion->update_state($cm, COMPLETION_COMPLETE);
        echo get_string('completed', 'wowslider');
    }
} else {
    die('Invalid action');
}
