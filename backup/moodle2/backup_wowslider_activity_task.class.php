<?php
/**
 * @package mod-wowslider
 * @category mod
 * @author Valery Fremaux (valery.fremaux@gmail.com)
 */
require_once($CFG->dirroot . '/mod/wowslider/backup/moodle2/backup_wowslider_stepslib.php');

class backup_wowslider_activity_task extends backup_activity_task {

    protected function define_my_settings() {
    }

    protected function define_my_steps() {
        $this->add_step(new backup_wowslider_activity_structure_step('wowslider_structure', 'wowslider.xml'));
    }

    static public function encode_content_links($content) {
        global $CFG;

        return $content;

        $base = preg_quote($CFG->wwwroot . '/mod/wowslider', '#');

        $pattern = "#(" . $base . "\/index.php\?id\=)([0-9]+)#";
        $content = preg_replace($pattern, '$@WOWSLIDERINDEX*$2@$', $content);

        $pattern = "#(" . $base . "\/view.php\?id\=)([0-9]+)#";
        $content = preg_replace($pattern, '$@WOWSLIDERVIEWBYID*$2@$', $content);

        $pattern = "#(" . $base . "\/report.php\?id\=)([0-9]+)#";
        $content = preg_replace($pattern, '$@WOWSLIDERREPORT*$2@$', $content);

        $pattern = "#(" . $base . "\/edit.php\?id\=)([0-9]+)#";
        $content = preg_replace($pattern, '$@WOWSLIDEREDIT*$2@$', $content);

        return $content;
    }

}
