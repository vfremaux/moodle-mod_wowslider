<?php
// This file is part of the mplayer plugin for Moodle - http://moodle.org/
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
 * @licence http://www.gnu.org/copyleft/gpl.html GNU Public Licence
 */
defined('MOODLE_INTERNAL') || die();

class mod_wowslider_renderer extends plugin_renderer_base {

    function print_body($wowslider) {
        global $CFG, $DB;

        if (!is_object($wowslider)) {
            return '';
        }

        $template = new StdClass;

        $i = 0;
        $template->images = array();

        $template->scriptvimeo = false;
        $template->scriptyoutube = false;
        $template->autoplayvideo = $wowslider->autoplayvideo;
        $template->sliderid = $wowslider->id;
        $template->bullets = $wowslider->bullets;

        foreach ($wowslider->images as $img) {

            $imageslot = new StdClass;

            $params = array('filename' => $img->filename, 'wowsliderid' => $wowslider->id);
            $linkdata = $DB->get_record('wowslider_slide', $params);
            $imageslot->ix = $i;

            if (@$linkdata->video) {
                if (strpos(@$linkdata->video, 'youtube') !== false) {
                    $scriptyoutube = true;
                    $imageslot->youtubeembed = str_replace('watch?v=', 'embed/', @$linkdata->video);
                }
                if (strpos(@$linkdata->video, 'vimeo') !== false) {
                    $scriptvimeo = true;
                    $imageslot->vimeoembed = str_replace('http://vimeo.com/', 'https://player.vimeo.com/video/', @$linkdata->video);
                }
            }

            $imageslot->linkurl = $linkdata->url;
            $imageslot->title = @$linkdata->title;
            $imageslot->url = $img->url;

            $template->images[] = $imageslot;

            $tooltipsarr[$i] = ''.@$linkdata->tooltip;

            // If no explicit tool tips use images.
            if (empty($wowslider->tooltips)) {
                $tooltip = new StdClass;
                $tooltip->title = @$linkdata->title;
                $tooltip->ix = $i + 1;
                if ($wowslider->bullets) {
                    $tooltip->url = $img->url;
                    $tooltip->tooltip = @$linkdata->tooltip;
                }
                $template->tooltips[] = $tooltip;
            }

            $i++;
        }

        // Get tooltips from additional filearea.
        $j = 0;
        if (!empty($wowslider->tooltips)) {
            foreach ($wowslider->tooltips as $tooltipimg) {
                $tooltip = new StdClass;
                $tooltip->title = @$tooltipsarr[$j];
                $tooltip->ix = $i + 1;
                if ($wowslider->bullets) {
                    $tooltip->url = $img->url;
                    $tooltip->tooltip = @$linkdata->tooltip;
                }
                $template->tooltips[] = $tooltip;
                if ($wowslider->bullets) {
                    $tooltip->url = $img->url;
                    $tooltip->tooltip = @$tooltipsarr[$j];
                }
                $template->tooltips[] = $tooltip;
                $j++;
            }
        }

        $template->bulletsid = $i;

        $detector = new Mobile_Detect();
        $template->ismobile = $detector->isMobile();

        $width = (is_numeric($wowslider->width)) ? $wowslider->width.'px' : $wowslider->width;

        $template->width = $width;
        $template->height = $wowslider->height . 'px';
 
        $template->sliderstyle = '<style>';
        if (empty($wowslider->showstartbutton)) {
            $template->sliderstyle .= '
               #wowslider-container .ws_playpause {
                    visibility:hidden !important;
               }
               #wowslider-container .ws_play {
                    visibility:hidden !important;
               }
            ';
        }
        $template->sliderstyle .='    #wowslider-container {
                max-width: ' . $template->width . ';
                max-height:' . $template->height . ';
            }
            * html #wowslider-container{ width:' . $template->width . '; }
            #wowslider-container .ws_images{
                max-width: ' . $template->width . ';
                max-height:' . $template->height . ';
            }
            #wowslider-container .ws_images ul a{
                 max-height:' . $template->height . ';
            }
            #wowslider-container .ws_images > div > img {max-height:' . $template->height . ';}
        </style>';


        return $this->render_from_template('mod_wowslider/sliderbody', $template);

    }
}