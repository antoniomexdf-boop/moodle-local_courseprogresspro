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

defined('MOODLE_INTERNAL') || die();

/**
 * Plugin settings for Local Course Progress Pro.
 *
 * @package    local_courseprogresspro
 * @copyright  2026 Jesus Antonio Jimenez Avina <antoniomexdf@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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

    $settings->add(new admin_setting_configselect(
        'local_courseprogresspro/mainprogresssource',
        get_string('settingsmainprogresssource', 'local_courseprogresspro'),
        get_string('settingsmainprogresssource_desc', 'local_courseprogresspro'),
        'moodle',
        [
            'moodle' => get_string('settingsmainprogresssource_moodle', 'local_courseprogresspro'),
            'custom' => get_string('settingsmainprogresssource_custom', 'local_courseprogresspro'),
        ]
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_courseprogresspro/countresources',
        get_string('settingscountresources', 'local_courseprogresspro'),
        get_string('settingscountresources_desc', 'local_courseprogresspro'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_courseprogresspro/resourcesrequirecompletion',
        get_string('settingsresourcesrequirecompletion', 'local_courseprogresspro'),
        get_string('settingsresourcesrequirecompletion_desc', 'local_courseprogresspro'),
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
