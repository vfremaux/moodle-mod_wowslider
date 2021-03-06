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
        $paths[] = new restore_path_element('slide', '/activity/wowslider/slides/slide');

        if ($this->get_setting_value('userinfo')) {
            $paths[] = new restore_path_element('view', '/activity/wowslider/views/view');
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

    protected function process_slide($data) {
        global $DB;

        $data = (object)$data;

        $oldid = $data->id;
        unset($data->id);

        $data->wowsliderid = $this->get_mappingid('wowslider', $data->wowsliderid);

        $newid = $DB->insert_record('wowslider_slide', $data);
    }

    protected function process_view($data) {
        global $DB;

        $data = (object)$data;

        $oldid = $data->id;
        unset($data->id);

        $data->wowsliderid = $this->get_mappingid('wowslider', $data->wowsliderid);
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newid = $DB->insert_record('wowslider_view', $data);
    }

    protected function after_execute() {
        // Add wowsliders related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_wowslider', 'intro', null);
        $this->add_related_files('mod_wowslider', 'wowslides', null);
        $this->add_related_files('mod_wowslider', 'tooltips', null);
    }
}
