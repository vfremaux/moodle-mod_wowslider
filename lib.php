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
 * Library of functions and constants for module wowslider
 *
 * @author 
 * @package mod_wowslider
 * @category mod
 **/
require_once($CFG->dirroot.'/mod/wowslider/locallib.php');

/**
 * List of features supported in Vodeclic module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function wowslider_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_OTHER;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_GROUPMEMBERSONLY:        return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;

        default: return null;
    }
}

/**
 * Given an object containing all the necessary data, 
 * (defined by the form in mod.html) this function 
 * will create a new instance and return the id number 
 * of the new instance.
 *
 * @param object $instance An object from the form in mod.html
 * @return int The id of the newly inserted wowslider record
 **/
function wowslider_add_instance($wowslider) {
    global $DB;

    if (!isset($wowslider->autoplay)) $wowslider->autoplay = 0;
    if (!isset($wowslider->autoplayvideo)) $wowslider->autoplayvideo = 0;
    if (!isset($wowslider->stoponhover)) $wowslider->stoponhover = 0;
    if (!isset($wowslider->playloop)) $wowslider->playloop = 0;
    if (!isset($wowslider->bullets)) $wowslider->bullets = 0;
    if (!isset($wowslider->caption)) $wowslider->caption = 0;
    if (!isset($wowslider->controls)) $wowslider->controls = 0;
    if (!isset($wowslider->fullscreen)) $wowslider->fullscreen = 0;
    if (!isset($wowslider->showstartbutton)) $wowslider->showstartbutton = 0;
    if (!isset($wowslider->lockdragslides)) $wowslider->lockdragslides = 0;
    if (!isset($wowslider->notificationslide)) $wowslider->notificationslide = 0;

    $wowslider->timemodified = time();

    $wowslider->id = $return = $DB->insert_record('wowslider', $wowslider);

    // saves draft customization image files into definitive filearea
    $instancefiles = wowslider_get_fileareas();
    foreach ($instancefiles as $if) {
        wowslider_save_draft_file($wowslider, $if);
    }

    return $return;
}

/**
 * Given an object containing all the necessary data, 
 * (defined by the form in mod.html) this function 
 * will update an existing instance with new data.
 *
 * @param object $instance An object from the form in mod.html
 * @return boolean Success/Fail
 **/
function wowslider_update_instance($wowslider) {
    global $DB;

    // If changing mode, we need delete all previous user dataas they are NOT relevant any more.
    // @TODO : add notification in mod_form to alert users...

    if (!isset($wowslider->autoplay)) $wowslider->autoplay = 0;
    if (!isset($wowslider->autoplayvideo)) $wowslider->autoplayvideo = 0;
    if (!isset($wowslider->stoponhover)) $wowslider->stoponhover = 0;
    if (!isset($wowslider->playloop)) $wowslider->playloop = 0;
    if (!isset($wowslider->bullets)) $wowslider->bullets = 0;
    if (!isset($wowslider->caption)) $wowslider->caption = 0;
    if (!isset($wowslider->controls)) $wowslider->controls = 0;
    if (!isset($wowslider->fullscreen)) $wowslider->fullscreen = 0;
    if (!isset($wowslider->showstartbutton)) $wowslider->showstartbutton = 0;
    if (!isset($wowslider->lockdragslides)) $wowslider->lockdragslides = 0;
    if (!isset($wowslider->notificationslide)) $wowslider->notificationslide = 0;

    $wowslider->timemodified = time();
    $wowslider->id = $wowslider->instance;

    if (!isset($wowslider->timemodified)) $wowslider->timemodified = 0;
    if (!isset($wowslider->timecreated)) $wowslider->timecreated = 0;

    // Saves draft customization image files into definitive filearea.
    $instancefiles = wowslider_get_fileareas();
    foreach ($instancefiles as $if) {
        wowslider_save_draft_file($wowslider, $if);
    }

    return $DB->update_record('wowslider', $wowslider);
    die;
}

/**
 * Given an ID of an instance of this module, 
 * this function will permanently delete the instance 
 * and any data that depends on it. 
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 **/
function wowslider_delete_instance($id) {
    global $DB;

    if (!$wowslider = $DB->get_record('wowslider', array('id' => "$id"))) {
        return false;
    }

    if (!$cm = get_coursemodule_from_instance('wowslider', $wowslider->id)) {
        return false;
    }

    $context = context_module::instance($cm->id);

    $result = true;

    # Delete any dependent records here.

    if (! $DB->delete_records('wowslider', array('id' => "$wowslider->id"))) {
        $result = false;
    }

    // Delete all files attached to this context.
    $fs = get_file_storage();
    $fs->delete_area_files($context->id);

    return $result;
}

/**
 * Return a small object with summary information about what a 
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 **/
function wowslider_user_outline($course, $user, $mod, $wowslider) {
    global $DB;

    if ($answers = $DB->get_records('wowslider_useranswer', array('userid' => $user->id))) {
        $firstanswer = array_pop($answers);
        $result = new stdClass();
        $result->info = get_string('wowsliderattempted', 'wowslider') . ': ' . userdate($firstanswer->timeanswered);
    } else {
        return null;
    }

    return $result;
}

/**
 * Print a detailed representation of what a user has done with 
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function wowslider_user_complete($course, $user, $mod, $wowslider) {
    global $DB;

    if ($accesses = $DB->get_records_select('log', " userid = ? AND module = 'wowslider' and action = 'view' ", array($user->id))) {
        echo '<br/>';
        echo get_string('wowslideraccesses', 'wowslider', count($accesses)) ;
    }

    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in wowslider activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
 */
function wowslider_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;

    return false;  //  True if anything was printed, otherwise false.
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ... 
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
 **/
function wowslider_cron () {
    global $CFG;

    return true;
}

/**
 * Must return an array of grades for a given instance of this module,
 * indexed by user.  It also returns a maximum allowed grade.
 * 
 * Example:
 *    $return->grades = array of grades;
 *    $return->maxgrade = maximum allowed grade;
 *
 *    return $return;
 *
 * @param int $wowsliderid ID of an instance of this module
 * @return mixed Null or object with an array of grades and with the maximum grade
 **/
function wowslider_grades($wowsliderid) {
   return NULL;
}

/**
 *
 **/
function wowslider_scale_used_anywhere($scaleid){
    global $DB;

    return false;
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of wowslider. Must include every user involved
 * in the instance, independent of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $wowsliderid ID of an instance of this module
 * @return mixed boolean/array of students
 **/
function wowslider_get_participants($wowsliderid) {
    global $CFG, $DB;

    $sql = "
        SELECT
            u.*
        FROM
            {user} u,
            {wowslider_useranswer} ua
        WHERE
            u.id = ua.userid AND
            ua.wowsliderid = ?
    ";
    if (!$records = $DB->get_records_sql($sql, array($wowsliderid))) {
        return false;
    }
    return $records;
}

/**
 * This function returns if a scale is being used by one wowslider
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $wowsliderid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 **/
function wowslider_scale_used ($wowsliderid,$scaleid) {
    $return = false;

    //$rec = get_record("wowslider","id","$wowsliderid","scale","-$scaleid");
    //
    //if (!empty($rec)  && !empty($scaleid)) {
    //    $return = true;
    //}
    return $return;
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove all responses from the specified wowslider
 * and clean up any related data.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function wowslider_reset_userdata($data) {
    global $CFG, $DB;

    $status = false;

    if ($wowsliders = $DB->get_records('wowslider', array('course' => $data->courseid))) {
        $status = true;
        foreach ($wowsliders as $ws) {
            $DB->delete_records('wowslider_slide_view', array('wowsliderid' => $ws->id));
        }
    }

    return $status;
}

function wowslider_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    $guests = false;
    if ($course->id > SITEID) {
        $enrols = enrol_get_instances($course->id, true);
        foreach($enrols as $e) {
            if ($e->enrol == 'guest') {
                $guests = true;
                break;
            }
        }
    }

    if (!$guests) {
        if ($cm->instance != $CFG->localmywowsliderid) {
            require_course_login($course, true, $cm);
        } else {
            // Just check is a logged in user.
            require_login();
        }
    }

    $fileareas = wowslider_get_fileareas();
    if (!in_array($filearea, $fileareas)) {
        return false;
    }

    $itemid = (int)array_shift($args);

    if (!$wowslider = $DB->get_record('wowslider', array('id' => $cm->instance))) {
        return false;
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_wowslider/$filearea/$itemid/$relativepath";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }

    // Finally send the file.
    send_stored_file($file, 0, 0, false); // Download MUST be forced - security!
}

/**
 * Obtains the automatic completion state for this module based on any conditions
 * in wowslider settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 */
function wowslider_get_completion_state($course, $cm, $userid, $type) {
    global $CFG, $DB;

    $wowsliderinstance = $DB->get_record('wowslider', array('id' => $cm->instance));

    // If completion option is enabled, evaluate it and return true/false.
    if ($wowsliderinstance->completionmediaviewed) {
        $finished = $DB->get_field('wowslider_slide_view', 'timecomplete', array('userid' => $userid, 'wowsliderid' => $cm->instance));
        if ($type == COMPLETION_AND) {
            $result = $result && $finished;
        } else {
            $result = $result || $finished;
        }
    } else {
        // Completion option is not enabled so just return $type.
        return $type;
    }

    return $result;
}
