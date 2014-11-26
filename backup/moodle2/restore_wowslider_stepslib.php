<?php
/**
 * @package mod-wowslider
 * @category mod
 * @author Valery Fremaux (valery.fremaux@gmail.com)
 */
class restore_wowslider_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('wowslider', '/activity/wowslider');

        if ($this->get_setting_value('userinfo')) {
        }

        return $this->prepare_activity_structure($paths);
    }

    protected function process_wowslider($data) {
        global $DB;

        $data = (object)$data;

        $oldid = $data->id;
        unset($data->id);

        $data->course = $this->get_courseid();
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newid = $DB->insert_record('wowslider', $data);
        $this->apply_activity_instance($newid);
        $this->set_mapping('wowslider', $oldid, $newid, true);
    }
}
