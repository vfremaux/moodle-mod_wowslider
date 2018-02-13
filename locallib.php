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
 * @author Valery Fremaux (valery.fremaux@gmail.com)
 * @package mod_wowslider
 * @category mod
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/wowslider/Mobile_Detect.php');

class WowSlider {

    protected $cm;

    protected $images;

    protected $tooltips;

    protected $wowsliderrec;

    function __construct($instanceidorrec, $cm) {
        global $DB;

        $this->cm = $cm;
        // Todo : check $cm / $instanceid integrity.

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

    function __get($attr) {
        if ($attr == 'images') {
            return $this->images;
        }
        if ($attr == 'tooltips') {
            return $this->tooltips;
        }
        return $this->wowsliderrec->$attr;
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
                $img->url = moodle_url::make_pluginfile_url($file->get_contextid(), 'mod_wowslider', $filearea, 0,
                                                            $file->get_filepath(), $file->get_filename());
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

        $scriptvimeo = false;
        $scriptyoutube = false;

        foreach ($this->images as $img) {

            $params = array('filename' => $img->filename, 'wowsliderid' => $this->wowsliderrec->id);
            $linkdata = $DB->get_record('wowslider_slide', $params);

            $videohtml = '';
            if (@$linkdata->video) {
                if (strpos(@$linkdata->video, 'youtube') !== false) {
                    $scriptyoutube = true;
                    $embed = str_replace('watch?v=', 'embed/', @$linkdata->video);
                    $videohtml =  '<iframe width="100%" height="100%" src="' . $embed . '?autoplay=' . $this->wowsliderrec->autoplayvideo . '&rel=0&enablejsapi=1&playerapiid=ytplayer&wmode=transparent" frameborder="0"></iframe>';
                }
                if (strpos(@$linkdata->video, 'vimeo') !== false) {
                    $scriptvimeo = true;
                    $embed = str_replace('http://vimeo.com/', 'https://player.vimeo.com/video/', @$linkdata->video);
                    $videohtml =  '<iframe width="100%" height="100%" src="' . $embed . '?autoplay=' . $this->wowsliderrec->autoplayvideo . '&title=0&byline=0&badge=0&portrait=0&api=1&player_id="wows'.$this->wowsliderrec->id.'_'.$i.'" id="wows'.$this->wowsliderrec->id.'_'.$i.'"></iframe>';
                }
            }

            if (empty($linkdata->url)) {
                $images .= '<li>'.$videohtml.'<img src="'.$img->url.'" alt="'.@$linkdata->title.'" title="'.@$linkdata->title.'" id="wows'.$this->wowsliderrec->id.'_'.$i.'"/></li>';
            } else {
                $images .= '<li><a href="'.$linkdata->url.'">'.$videohtml.'<img src="'.$img->url.'" alt="'.@$linkdata->title.'" title="'.@$linkdata->title.'" id="wows'.$this->wowsliderrec->id.'_'.$i.'"/></a></li>';
            }

            $tooltipsarr[$i] = ''.@$linkdata->tooltip;

            // If no explicit tool tips use images.
            if (empty($this->tooltips)) {
                $tooltips .= '<a href="#" title="'.@$linkdata->title.'" class="j_bullets" data-id="'.($i + 1).'">';
                if ($this->wowsliderrec->bullets) {
                     $tooltips .= '<img width="90" src="'.$img->url.'" alt="'.@$linkdata->tooltip.'"/>';
                }
                $tooltips .= ($i + 1).'</a>';
            }

            $i++;
        }

        // Get tooltips from additional filearea.
        $j = 0;
        if (!empty($this->tooltips)) {
            foreach ($this->tooltips as $tooltipimg) {
                $tooltips .= '<a href="#" title="'.@$tooltipsarr[$j].'" class="j_bullets" data-id="'.($j + 1).'">';
                if ($this->wowsliderrec->bullets) {
                     $tooltips .= '<img width="90" src="'.$tooltipimg->url.'" alt="'.@$tooltipsarr[$j].'"/>';
                }
                $tooltips .= ($j + 1).'</a>';
                $j++;
            }
        }

        $c_bullets = $i;
        $detector = new Mobile_Detect();
        $width = (is_numeric($this->wowsliderrec->width)) ? $this->wowsliderrec->width.'px' : $this->wowsliderrec->width;

        $sliderwidth = $this->wowsliderrec->width . 'px';
        $sliderheight = $this->wowsliderrec->height . 'px';
        $sliderstyle = '<style>';
        if (empty($this->wowsliderrec->showstartbutton)) {
            $sliderstyle .= '
               #wowslider-container .ws_playpause {
                    visibility:hidden !important;
               }
               #wowslider-container .ws_play {
                    visibility:hidden !important;
               }
            ';
        }
        $sliderstyle .='    #wowslider-container {
                max-width: ' . $sliderwidth . ';
                max-height:' . $sliderheight . ';
            }
            * html #wowslider-container{ width:' . $sliderwidth . '; }
            #wowslider-container .ws_images{
                max-width: ' . $sliderwidth . ';
                max-height:' . $sliderheight . ';
            }
            #wowslider-container .ws_images ul a{
                 max-height:' . $sliderheight . ';
            }
            #wowslider-container .ws_images > div > img {max-height:' . $sliderheight . ';}
        </style>';

        if ($scriptyoutube) {
            $sliderbody .= '<script src="https://www.youtube.com/iframe_api"></script>';
        }
        if ($scriptvimeo) {
            $sliderbody .= '<script src="http://a.vimeocdn.com/js/froogaloop2.min.js"></script>';
        }

        $sliderbody .= $sliderstyle;
        if ($detector->isMobile()) {
            $sliderbody .= '<div id="wow-wrapper" style="width:100%;margin:auto">';
            $sliderbody .= '<div id="wowslider-container" style="width:100%">';
        } else {
            $sliderbody .= '<div id="wow-wrapper" style="width:'.$width.';margin:auto">';
            $sliderbody .= '<div id="wowslider-container">';
        }
        if (!$detector->isMobile()) {
            $sliderbody .= '<div class="ws_images" style="height:'.$this->wowsliderrec->height.'px"><ul>';
        } else {
            $sliderbody .= '<div class="ws_images"><ul>';
        }

        $sliderbody .= $images;
        $sliderbody .= '</ul></div>
        <div class="ws_bullets" data-id="'.$c_bullets.'"><div>
        '.$tooltips.'
        </div></div>
        <div class="ws_shadow"></div>
        </div>';

        if ($this->wowsliderrec->notificationslide) {
            $sliderbody .= '<div class="notif_slide"></div>';
        }

        $sliderbody .= '</div>';

        return $sliderbody;
    }

    /**
     * Ensures js material for wowsliders is loaded
     *
     */
    function require_js() {
        global $PAGE;

        $scriptpath = (empty($this->wowsliderrec->effect)) ? 'basic' : $this->wowsliderrec->effect;

        $PAGE->requires->jquery();

        $PAGE->requires->js('/mod/wowslider/js/'.$scriptpath.'/wowslider.js', false);
        $PAGE->requires->js('/mod/wowslider/js/'.$scriptpath.'/script.js', false);
        $PAGE->requires->js('/mod/wowslider/js/script.php?wid='.$this->wowsliderrec->id, false);

        if ($this->wowsliderrec->notificationslide) {
            $PAGE->requires->js('/mod/wowslider/js/attrchange.js', false);
            $context = context_module::instance($this->cm->id);
            if (isloggedin() && !is_guest($context)) {
                $PAGE->requires->js('/mod/wowslider/js/attrchangecallback.js', false);
            }
        }

        if (!empty($this->wowsliderrec->lockdragslides)) {
            $PAGE->requires->js('/mod/wowslider/js/lockdrag.js', false);
        }
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
        if (!$fs->is_area_empty($usercontext->id, 'user', 'draft', $filepickeritemid, true)) {
            $filearea = str_replace('fileid', '', $filearea);
            file_save_draft_area_files($filepickeritemid, $context->id, 'mod_wowslider', $filearea, 0);
            if ($savedfiles = $fs->get_area_files($context->id, 'mod_wowslider', $filearea, 0)) {
                // Prepare and update metadata records.
                if ($filearea == 'wowslides') {
                    $mtdrecords = $DB->get_records('wowslider_slide', array('wowsliderid' => $wowslider->id), 'filename', 'filename,id,url,title,tooltip');
                    foreach ($savedfiles as $afile) {
                        $filename= $afile->get_filename();
                        if ($afile->is_directory()) {
                            continue;
                        }
                        if (!$DB->record_exists('wowslider_slide', array('wowsliderid' => $wowslider->id, 'filename' => $filename))) {
                            $mtdrec = new StdClass();
                            $mtdrec->wowsliderid = $wowslider->id;
                            $mtdrec->filename = $filename;
                            $DB->insert_record('wowslider_slide', $mtdrec);
                        } else {
                            // Just unmark it.
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

/**
 * this is not a yet needed function but could be in some future.
 * @param stored_file $file the image file to process
 * @param int $maxwidth the maximum width allowed for the image
 * @param int $maxheight the maximum height allowed for the image
 * @return string HTML fragment to add to the label
 */
function wowslider_generate_thumb_image(stored_file $file, $maxwidth, $maxheight) {
    $fullurl = moodle_url::make_draftfile_url($file->get_itemid(), $file->get_filepath(), $file->get_filename());
    $link = null;
    $attrib = array('alt' => $file->get_filename(), 'src' => $fullurl);

    if ($imginfo = $file->get_imageinfo()) {
        // Work out the new width / height, bounded by maxwidth / maxheight.
        $width = $imginfo['width'];
        $height = $imginfo['height'];
        if (!empty($maxwidth) && $width > $maxwidth) {
            $height *= (float)$maxwidth / $width;
            $width = $maxwidth;
        }
        if (!empty($maxheight) && $height > $maxheight) {
            $width *= (float)$maxheight / $height;
            $height = $maxheight;
        }

        $attrib['width'] = $width;
        $attrib['height'] = $height;

        // If the size has changed and the image is of a suitable mime type, generate a smaller version
        if ($width != $imginfo['width']) {
            $mimetype = $file->get_mimetype();
            if ($mimetype === 'image/gif' or $mimetype === 'image/jpeg' or $mimetype === 'image/png') {
                $tmproot = make_temp_directory('mod_wowslider');
                $tmpfilepath = $tmproot.'/'.$file->get_contenthash();
                $file->copy_content_to($tmpfilepath);
                $data = generate_image_thumbnail($tmpfilepath, $width, $height);
                unlink($tmpfilepath);

                if (!empty($data)) {
                    $fs = get_file_storage();
                    $record = array(
                        'contextid' => $file->get_contextid(),
                        'component' => 'mod_wowslider',
                        'filearea'  => 'tooltips',
                        'itemid'    => $file->get_itemid(),
                        'filepath'  => '/thumb/',
                        'filename'  => $file->get_filename(),
                    );
                    $smallfile = $fs->create_file_from_string($record, $data);

                    // Replace the image 'src' with the resized file and link to the original.
                    $attrib['src'] = moodle_url::make_draftfile_url($smallfile->get_itemid(), $smallfile->get_filepath(),
                        $smallfile->get_filename());
                    $link = $fullurl;
                }
            }
        }

    } else {
        // Assume this is an image type that get_imageinfo cannot handle (e.g. SVG).
        $attrib['width'] = $maxwidth;
    }

    $img = html_writer::empty_tag('img', $attrib);
    if ($link) {
        return html_writer::link($link, $img);
    } else {
        return $img;
    }
}
