<?php

// This file keeps track of upgrades to the mplayer module
//
// The commands in here will all be database-neutral, using the functions defined in lib/ddllib.php

function xmldb_wowslider_upgrade($oldversion=0) {
    global $CFG, $THEME, $DB;

    $result = true;

    $dbman = $DB->get_manager();

    return $result;
}
