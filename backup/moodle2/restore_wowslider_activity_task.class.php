<?php 
/**
 * @package mod-wowslider
 * @category mod
 * @author Valery Fremaux (valery.fremaux@gmail.com)
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/wowslider/backup/moodle2/restore_wowslider_stepslib.php');

class restore_wowslider_activity_task extends restore_activity_task {

    protected function define_my_settings() {}

    protected function define_my_steps() {
        $this->add_step(new restore_wowslider_activity_structure_step('wowslider_structure', 'wowslider.xml'));
    }

    static public function define_decode_contents() {

        $contents = array();
        $contents[] = new restore_decode_content('wowslider', array('intro'), 'wowslider');
        //$contents[] = new restore_decode_content('wowslider_entries', array('text', 'entrycomment'), 'wowslider_entry');

        return $contents;
    }

    static public function define_decode_rules() {
        return array();
        $rules = array();
        $rules[] = new restore_decode_rule('WOWSLIDERINDEX', '/mod/wowslider/index.php?id=$1', 'course');
        $rules[] = new restore_decode_rule('WOWSLIDERVIEWBYID', '/mod/wowslider/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('WOWSLIDERREPORT', '/mod/wowslider/report.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('WOWSLIDEREDIT', '/mod/wowslider/edit.php?id=$1', 'course_module');

        return $rules;

    }

    static public function define_restore_log_rules() {
        $rules = array();

        return $rules;
    }
}
