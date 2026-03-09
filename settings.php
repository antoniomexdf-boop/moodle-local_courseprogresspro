<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_courseprogresspro', get_string('pluginname', 'local_courseprogresspro'));
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_heading(
        'local_courseprogresspro/general',
        get_string('settingsgeneral', 'local_courseprogresspro'),
        ''
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_courseprogresspro/enabled',
        get_string('settingsenabled', 'local_courseprogresspro'),
        get_string('settingsenabled_desc', 'local_courseprogresspro'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_courseprogresspro/countresources',
        get_string('settingscountresources', 'local_courseprogresspro'),
        get_string('settingscountresources_desc', 'local_courseprogresspro'),
        1
    ));

    $settings->add(new admin_setting_configselect(
        'local_courseprogresspro/quizmode',
        get_string('settingsquizmode', 'local_courseprogresspro'),
        get_string('settingsquizmode_desc', 'local_courseprogresspro'),
        'questions',
        [
            'questions' => get_string('settingsquizmode_questions', 'local_courseprogresspro'),
            'activity' => get_string('settingsquizmode_activity', 'local_courseprogresspro'),
        ]
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_courseprogresspro/showpercentage',
        get_string('settingsshowpercentage', 'local_courseprogresspro'),
        get_string('settingsshowpercentage_desc', 'local_courseprogresspro'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_courseprogresspro/showcompletedcount',
        get_string('settingsshowcompletedcount', 'local_courseprogresspro'),
        get_string('settingsshowcompletedcount_desc', 'local_courseprogresspro'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_courseprogresspro/showpendingbutton',
        get_string('settingsshowpendingbutton', 'local_courseprogresspro'),
        get_string('settingsshowpendingbutton_desc', 'local_courseprogresspro'),
        1
    ));

    $settings->add(new admin_setting_configtext(
        'local_courseprogresspro/headertext',
        get_string('settingsheadertext', 'local_courseprogresspro'),
        get_string('settingsheadertext_desc', 'local_courseprogresspro'),
        get_string('progresslabel', 'local_courseprogresspro'),
        PARAM_TEXT
    ));
}
