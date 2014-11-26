<?php

/**
 * @package mod-wowslider
 * @category mod
 * @author Valery Fremaux (valery.fremaux@gmail.com)
 */
class backup_wowslider_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        $wowslider = new backup_nested_element('wowslider', array('id'), array(
            'name', 'intro', 'introformat', 'timecreated', 'timemodified', 'width', 'height', 'skin'));

        // Sources
        $wowslider->set_source_table('wowslider', array('id' => backup::VAR_ACTIVITYID));

        if ($this->get_setting_value('userinfo')) {
        }

        // Define file annotations
        $wowslider->annotate_files('mod_wowslider', 'intro', null); // This file areas haven't itemid
        $deck->annotate_files('mod_wowslider', 'wowsliderfile', 'id');

        return $this->prepare_activity_structure($wowslider);
    }
}
