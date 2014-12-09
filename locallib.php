<?php
require_once($CFG->dirroot.'/mod/wowslider/Mobile_Detect.php');

class WowSlider {

    var $cm;

    var $images;

    var $wowsliderrec;

    function __construct($instanceidorrec, $cm) {
        global $DB;

        $this->cm = $cm;
        // todo : check $cm / $instanceid integrity

        if (is_numeric($instanceidorrec)) {
            if (!$this->wowsliderrec = $DB->get_record('wowslider', array('id' => $instanceidorrec))) {
                return null;
            }
        } else {
            $this->wowsliderrec = $instanceidorrec;
        }

        $this->images = $this->load_images('wowslides');
        $this->tooltips = $this->load_images('tooltips');
    }

    private function load_images($filearea) {
        $fs = get_file_storage();

        $context = context_module::instance($this->cm->id);
        $images = $fs->get_area_files($context->id, 'mod_wowslider', $filearea, 0);
        $loadedimages = array();
        if ($images) {
            foreach ($images as $file) {
                if ($file->is_directory()) {
                    continue;
                }
                $img = new StdClass;
                $img->url = moodle_url::make_pluginfile_url($file->get_contextid(), 'mod_wowslider', $filearea, 0, $file->get_filepath(), $file->get_filename());
                $img->title = '';
                $img->filename = $file->get_filename();
                $loadedimages[] = $img;
            }
        }
        return $loadedimages;
    }

    function print_body() {
        global $CFG, $DB;

        $i = 0;
        $images = '';
        $tooltips = '';
        $tooltipsarr = '';
        foreach ($this->images as $img) {
            $linkdata = $DB->get_record('wowslider_slide', array('filename' => $img->filename, 'wowsliderid' => $this->wowsliderrec->id));
            $tooltipsarr[$i] = @$linkdata->tooltip;
            $tooltitles[$i] = @$linkdata->title;
            if (empty($linkdata->url)) {
                $images .= '
                    <li><img src="'.$img->url.'" alt="'.@$linkdata->title.'" title="'.@$linkdata->title.'" id="wows'.$this->wowsliderrec->id.'_'.$i.'"/></li>';
            } else {
                $images .= '
                    <li><a href="'.$linkdata->url.'"><img src="'.$img->url.'" alt="'.@$linkdata->title.'" title="'.@$linkdata->title.'" id="wows'.$this->wowsliderrec->id.'_'.$i.'"/></a></li>';
            }
            $i++;
        }

        $i = 0;
        foreach ($this->tooltips as $img) {
            $tooltips .= '<a href="#" title="'.$tooltitles[$i].'"><img src="'.$img->url.'" alt="'.$tooltipsarr[$i].'"/>'.($i + 1).'</a>';
            $i++;
        }

        $detector = new Mobile_Detect();

        $slider_body = '<!-- Start WOWSlider.com BODY section -->';
        $width = (is_numeric($this->wowsliderrec->width)) ? $this->wowsliderrec->width.'px' : $this->wowsliderrec->width ;

        // At the moment only one slider instance per page...

        if ($detector->isMobile()) {
            $slider_body .= '<div id="wow-wrapper" style="width:100%;margin:auto">';
            $slider_body .= '<div id="wowslider-container1" style="width:100%">';
        } else {
            $slider_body .= '<div id="wow-wrapper" style="width:'.$width.';margin:auto">';
            $slider_body .= '<div id="wowslider-container1">';
        }
        if (!$detector->isMobile()) {
            $slider_body .= '<div class="ws_images" style="height:'.$this->wowsliderrec->height.'px"><ul>';
        } else {
            $slider_body .= '<div class="ws_images"><ul>';
        }
        // $slider_body .= '<div class="ws_images"><ul>
        $slider_body .= $images;
        $slider_body .= '</ul></div>
        <div class="ws_bullets"><div>
        '.$tooltips.'
        </div></div>
        <div class="ws_shadow"></div>
        </div>';

        $slider_body .= '</div>';

        $slider_body .= '<!-- End WOWSlider.com BODY section -->';

        return $slider_body;
    }

    /**
     * Ensures js material for wowsliders is loaded
     *
     */
    function require_js() {
        global $PAGE;

        $scriptpath = (empty($this->wowsliderrec->effect)) ? 'basic' : $this->wowsliderrec->effect;

        $PAGE->requires->js('/mod/wowslider/js/'.$scriptpath.'/wowslider.js', false);
        $PAGE->requires->js('/mod/wowslider/js/'.$scriptpath.'/script.js', false);
        $PAGE->requires->js('/mod/wowslider/js/script.php?wid='.$this->wowsliderrec->id, false);
    }

    /**
     * Ensures js material for wowsliders is loaded
     *
     */
    function require_css() {
        global $PAGE;

        $stylepath = (empty($this->wowsliderrec->skin)) ? 'basic' : $this->wowsliderrec->skin;

        $PAGE->requires->css('/mod/wowslider/css/'.$stylepath.'/style.css');
        $PAGE->requires->css('/mod/wowslider/css/'.$stylepath.'/style.mod.css');
    }
}

/**
 * Get all file areas used in module
 */
function wowslider_get_fileareas() {
    return array('intro', 'wowslides', 'tooltips');
}

function wowslider_save_draft_file(&$wowslider, $filearea) {
    global $USER, $DB;
    static $fs;

    $usercontext = context_user::instance($USER->id);
    $context = context_module::instance($wowslider->coursemodule);

    if (!isset($wowslider->$filearea)) {
        return;
    }

    $filepickeritemid = $wowslider->$filearea;

    if (!$filepickeritemid) {
        return;
    }

    if (empty($fs)) {
        $fs = get_file_storage();
    }

    if ($filearea == 'wowslides' || $filearea == 'tooltips') {
        if (!$fs->is_area_empty($usercontext->id, 'user', 'draft', $filepickeritemid, true)){
            $filearea = str_replace('fileid', '', $filearea);
            file_save_draft_area_files($filepickeritemid, $context->id, 'mod_wowslider', $filearea, 0);
            if ($savedfiles = $fs->get_area_files($context->id, 'mod_wowslider', $filearea, 0)) {
                // Prepare and update metadata records
                if ($filearea == 'wowslides') {
                    $mtdrecords = $DB->get_records('wowslider_slide', array('wowsliderid' => $wowslider->id), 'filename', 'filename,id,url,title,tooltip');
                    foreach ($savedfiles as $afile) {
                        $filename= $afile->get_filename();
                        if ($afile->is_directory()) continue;
                        if (!$DB->record_exists('wowslider_slide', array('wowsliderid' => $wowslider->id, 'filename' => $filename))) {
                            $mtdrec = new StdClass();
                            $mtdrec->wowsliderid = $wowslider->id;
                            $mtdrec->filename = $filename;
                            $DB->insert_record('wowslider_slide', $mtdrec);
                        } else {
                            // just unmark it
                            unset($mtdrecords[$filename]);
                        }
                    }
                    // Remove yet marked existing records.
                    foreach ($mtdrecords as $filename => $rec) {
                        $DB->delete_records('wowslider_slide', array('id' => $rec->id));
                    }
                }
            }
        }
    }
}

function wowslider_clear_area(&$wowslider, $filearea) {

    if (!$cm = get_coursemodule_from_instance('wowslider', $wowslider->id)) {
        return false;
    }

    $context = context_module::instance($cm->id);

    $fs = get_file_storage();
    $fs->delete_area_files($context->id, 'mod_wowslider', $filearea);
}
