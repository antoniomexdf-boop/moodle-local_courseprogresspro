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
 * English language strings for local_courseprogresspro.
 *
 * @package    local_courseprogresspro
 * @copyright  2026 Jesus Antonio Jimenez Avina <antoniomexdf@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Course Progress Pro';
$string['progresslabel'] = 'Your course progress';
$string['progresssummary'] = 'Completed: {$a}%';
$string['pendingcountsummary'] = 'What remains: {$a} pending actions';
$string['progressbarlabel'] = 'Course progress: {$a}%';
$string['settingsgeneral'] = 'General settings';
$string['settingsenabled'] = 'Enable plugin';
$string['settingsenabled_desc'] = 'If disabled, the progress UI is not shown to students.';
$string['settingsmainprogresssource'] = 'Main progress source';
$string['settingsmainprogresssource_desc'] = 'Defines whether the main progress bar follows Moodle course completion or the plugin custom calculation. Course sections are never counted as progress units.';
$string['settingsmainprogresssource_moodle'] = 'Moodle course completion';
$string['settingsmainprogresssource_custom'] = 'Plugin custom progress';
$string['settingscountresources'] = 'Count resources';
$string['settingscountresources_desc'] = 'Includes page, URL, file, book, and folder resources in the progress calculation. Course sections are used only for navigation context and are never counted.';
$string['settingsresourcesrequirecompletion'] = 'Require completion tracking for resources';
$string['settingsresourcesrequirecompletion_desc'] = 'When enabled, resources only count if the activity has Moodle completion tracking configured. This avoids using log-based view detection. Course sections are never counted because they do not provide completion evidence.';
$string['settingsquizmode'] = 'Quiz calculation mode';
$string['settingsquizmode_desc'] = 'Defines whether a quiz counts by answered questions or as a single activity.';
$string['settingsquizmode_questions'] = 'Count by questions';
$string['settingsquizmode_activity'] = 'Count as one activity';
$string['settingsshowpercentage'] = 'Show numeric percentage';
$string['settingsshowpercentage_desc'] = 'Displays the current progress percentage next to the bar.';
$string['settingsshowpendingbutton'] = 'Show pending actions button';
$string['settingsshowpendingbutton_desc'] = 'Displays a button that opens a window with the list of pending actions.';
$string['settingsheadertext'] = 'Header text';
$string['settingsheadertext_desc'] = 'Text shown above the progress bar.';
$string['pendingbuttonlabel'] = 'See what remains';
$string['pendingactivities'] = '{$a} activities remaining in this course';
$string['allactivitiesdone'] = 'You are up to date in this course';
$string['pendingmodaltitle'] = 'What remains in this course';
$string['closemodal'] = 'Close window';
$string['nopendingitems'] = 'There are no pending activities right now.';
$string['noactivitiesdetected'] = 'No visible activities were detected in this course.';
$string['pendingactivityitem'] = '{$a->name}';
$string['pendingquizitem'] = '{$a->name} (quiz in progress: {$a->count} questions remaining)';
$string['pendingstatusavailable'] = 'Available now';
$string['pendingstatuslocked'] = 'Not available yet';
$string['pendingopenactivity'] = 'Open activity';
$string['pendinggotosection'] = 'Go to section';
$string['privacy:metadata'] = 'The local_courseprogresspro plugin does not store personal data.';
