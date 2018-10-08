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

namespace mod_wowslider\privacy;

use \core_privacy\local\request\writer;
use \core_privacy\local\metadata\collection;

defined('MOODLE_INTERNAL') || die();

class provider implements \core_privacy\local\metadata\provider {

    public static function get_metadata(collection $collection) : collection {

        $fields = [
            'userid' => 'privacy:metadata:wowslider_views:useridid',
            'wowsliderid' => 'privacy:metadata:wowslider_views:wowsliderid',
            'views' => 'privacy:metadata:wowslider_views:views',
            'timefirstview' => 'privacy:metadata:wowslider_views:timefirstview',
            'timecomplete' => 'privacy:metadata:wowslider_views:timecomplete',
        ];

        $collection->add_database_table('wowslider_slide_view', $fields, 'privacy:metadata:wowslider_views');

        return $collection;
    }

}