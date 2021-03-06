<?php

/**
 * @package mod-wowslider
 * @category mod
 * @author Valery Fremaux (valery.fremaux@gmail.com)
 */
class backup_wowslider_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        $wowslider = new backup_nested_element('wowslider', array('id'), array(
            'name', 'intro', 'introformat', 'timecreated', 'timemodified', 'width', 'height', 'effect', 'skin', 
            'slideduration', 'delay', 'autoplay', 'autoplayvideo', 'stoponhover', 'playloop', 'bullets', 
            'caption', 'controls', 'showstartbutton', 'lockdragslides', 'fullscreen', 'notificationslide', 'completionmediaviewed'));

        $slides = new backup_nested_element('slides');

        $slide = new backup_nested_element('wowslider_slide', array('id'), array(
            'filename', 'url', 'title', 'tooltip'));

        $views = new backup_nested_element('views');

        $view = new backup_nested_element('wowslider_slide_view', array('id'), array(
            'userid', 'views', 'timefirstview', 'timecomplete'));

        // 
        $wowslider->add_child($slides);
        $slides->add_child($slide);

        $wowslider->add_child($views);
        $views->add_child($view);

        // Sources.
        $wowslider->set_source_table('wowslider', array('id' => backup::VAR_ACTIVITYID));

        $slide->set_source_table('wowslider_slide', array('id' => backup::VAR_ACTIVITYID));

        if ($this->get_setting_value('userinfo')) {
            $view->set_source_table('wowslider_slide_view', array('wowsliderid' => backup::VAR_ACTIVITYID));
        }

        $view->annotate_ids('userid', 'userid');

        // Define file annotations.
        $wowslider->annotate_files('mod_wowslider', 'intro', null); // This file areas haven't itemid
        $wowslider->annotate_files('mod_wowslider', 'wowslides', null); // This file areas haven't itemid
        $wowslider->annotate_files('mod_wowslider', 'tooltips', null); // This file areas haven't itemid

        return $this->prepare_activity_structure($wowslider);
    }
}
