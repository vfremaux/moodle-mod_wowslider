<?php

// This file keeps track of upgrades to the mplayer module
//
// The commands in here will all be database-neutral, using the functions defined in lib/ddllib.php

function xmldb_wowslider_upgrade($oldversion=0) {
    global $CFG, $THEME, $DB;

    $result = true;

    $dbman = $DB->get_manager();

    if ($oldversion < 2014112800) {

        $table = new xmldb_table('wowslider');
        $field = new xmldb_field('slideduration');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 4, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 20, 'skin');
        if (!$dbman->field_exists($table, $field)){
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('delay');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 4, XMLDB_UNSIGNED, XMLDB_NOTNULL, null,  20, 'slideduration');
        if (!$dbman->field_exists($table, $field)){
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('autoplay');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, XMLDB_NOTNULL, null,  1, 'delay');
        if (!$dbman->field_exists($table, $field)){
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('autoplayvideo');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, XMLDB_NOTNULL, null,  1, 'autoplay');
        if (!$dbman->field_exists($table, $field)){
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('stoponhover');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, XMLDB_NOTNULL, null,  1, 'autoplayvideo');
        if (!$dbman->field_exists($table, $field)){
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('playloop');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, XMLDB_NOTNULL, null,  1, 'stoponhover');
        if (!$dbman->field_exists($table, $field)){
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('bullets');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 3, XMLDB_UNSIGNED, XMLDB_NOTNULL, null,  1, 'playloop');
        if (!$dbman->field_exists($table, $field)){
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('caption');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, XMLDB_NOTNULL, null,  1, 'bullets');
        if (!$dbman->field_exists($table, $field)){
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('controls');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, XMLDB_NOTNULL, null,  1, 'caption');
        if (!$dbman->field_exists($table, $field)){
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('fullscreen');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, XMLDB_NOTNULL, null,  1, 'controls');
        if (!$dbman->field_exists($table, $field)){
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2014112800, 'wowslider');
    }

    if ($oldversion < 2014112801) {

        $table = new xmldb_table('wowslider');
        $field = new xmldb_field('notificationslide');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, XMLDB_NOTNULL, null,  1, 'fullscreen');
        if (!$dbman->field_exists($table, $field)){
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('wowslider_slide');
        $field = new xmldb_field('video');
        $field->set_attributes(XMLDB_TYPE_CHAR, 255, null, null, null,  null, 'tooltip');
        if (!$dbman->field_exists($table, $field)){
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2014112801, 'wowslider');
    }

    if ($oldversion < 2014112802) {
        $table = new xmldb_table('wowslider');
        $field = new xmldb_field('completionmediaviewed');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, XMLDB_NOTNULL, null,  1, 'notificationslide');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define table wowslider_slide_view to be created.
        $table = new xmldb_table('wowslider_slide_view');

        // Adding fields to table wowslider_slide_view.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('wowsliderid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('views', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timefirstview', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecomplete', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        // Adding keys to table wowslider_slide_view.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table wowslider_slide_view.
        $table->add_index('wowslider_user_ix', XMLDB_INDEX_NOTUNIQUE, array('wowsliderid', 'userid'));

        // Conditionally launch create table for wowslider_slide_view.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_mod_savepoint(true, 2014112802, 'wowslider');
    }

    if ($oldversion < 2015040200) {
        $table = new xmldb_table('wowslider');
        $field = new xmldb_field('showstartbutton');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, XMLDB_NOTNULL, null,  1, 'controls');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('lockdragslides');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, XMLDB_NOTNULL, null,  1, 'showstartbutton');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2015040200, 'wowslider');
    }
    return $result;
}
