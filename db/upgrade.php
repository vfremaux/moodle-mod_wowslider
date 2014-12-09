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


    return $result;
}
