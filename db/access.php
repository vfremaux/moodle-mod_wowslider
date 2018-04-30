<?php
/**
 * Capability definitions for SWF Activity Module.
 *
 * For naming conventions, see lib/db/access.php.
 * 
 * If you edit these capabilities, they won't take effect until you upgrade the module version.
 */
$capabilities = array(

    'mod/wowslider:addinstance' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),

    'mod/wowslider:edit' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
);

