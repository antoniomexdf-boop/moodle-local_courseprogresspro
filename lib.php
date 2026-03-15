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
 * Local Course Progress Pro helper functions.
 *
 * @package    local_courseprogresspro
 * @copyright  2026 Jesus Antonio Jimenez Avina <antoniomexdf@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Gets a plugin setting with fallback.
 *
 * @param string $name
 * @param mixed $default
 * @return mixed
 */
function local_courseprogresspro_get_setting(string $name, $default) {
    $value = get_config('local_courseprogresspro', $name);
    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return $value;
}

/**
 * Returns whether the plugin should render on the current page.
 *
 * @return bool
 */
function local_courseprogresspro_should_render(): bool {
    global $PAGE, $USER;

    if (!(int)local_courseprogresspro_get_setting('enabled', 1)) {
        return false;
    }

    if (empty($PAGE->course) || empty($PAGE->course->id) || (int)$PAGE->course->id === SITEID) {
        return false;
    }

    if (empty($USER->id) || isguestuser()) {
        return false;
    }

    return true;
}

/**
 * Builds the progress snapshot for the current user.
 *
 * @param stdClass $course
 * @param int $userid
 * @return array
 */
function local_courseprogresspro_get_snapshot(stdClass $course, int $userid): array {
    require_once(__DIR__ . '/../../lib/completionlib.php');

    $modinfo = get_fast_modinfo($course);
    $completioninfo = new completion_info($course);
    $settings = [
        'mainprogresssource' => (string)local_courseprogresspro_get_setting('mainprogresssource', 'moodle'),
        'countresources' => (int)local_courseprogresspro_get_setting('countresources', 1),
        'resourcesrequirecompletion' => (int)local_courseprogresspro_get_setting('resourcesrequirecompletion', 1),
        'quizmode' => (string)local_courseprogresspro_get_setting('quizmode', 'questions'),
    ];

    $completedunits = 0;
    $totalunits = 0;
    $pendingitems = [];

    foreach ($modinfo->get_cms() as $cm) {
        if (!local_courseprogresspro_should_count_cm($cm, $settings)) {
            continue;
        }

        $progress = local_courseprogresspro_get_cm_progress($cm, $userid, $completioninfo, $settings);
        $completedunits += $progress['completedunits'];
        $totalunits += $progress['totalunits'];

        if (!empty($progress['pendingdetail'])) {
            $pendingitems[] = $progress['pendingdetail'];
        }
    }

    if (($settings['mainprogresssource'] ?? 'moodle') === 'moodle') {
        $moodlesnapshot = local_courseprogresspro_get_moodle_progress_snapshot($modinfo, $completioninfo, $userid);
        $completedunits = $moodlesnapshot['completedunits'];
        $totalunits = $moodlesnapshot['totalunits'];
    }

    $percentage = 0;
    if ($totalunits > 0) {
        $percentage = (int)round(($completedunits / $totalunits) * 100);
    }

    usort($pendingitems, static function (array $a, array $b): int {
        return ($b['available'] ?? 0) <=> ($a['available'] ?? 0);
    });

    return [
        'completedunits' => $completedunits,
        'totalunits' => $totalunits,
        'percentage' => max(0, min(100, $percentage)),
        'pendingcount' => count($pendingitems),
        'pendingitems' => $pendingitems,
    ];
}

/**
 * Builds the main progress snapshot using Moodle completion data only.
 *
 * @param course_modinfo $modinfo
 * @param completion_info $completioninfo
 * @param int $userid
 * @return array
 */
function local_courseprogresspro_get_moodle_progress_snapshot(
    course_modinfo $modinfo,
    completion_info $completioninfo,
    int $userid
): array {
    $completedunits = 0;
    $totalunits = 0;

    foreach ($modinfo->get_cms() as $cm) {
        if (!$cm->visible) {
            continue;
        }

        if ($completioninfo->is_enabled($cm) == COMPLETION_TRACKING_NONE) {
            continue;
        }

        $totalunits++;
        $data = $completioninfo->get_data($cm, true, $userid);
        if (!empty($data->completionstate) && (int)$data->completionstate !== COMPLETION_INCOMPLETE) {
            $completedunits++;
        }
    }

    return [
        'completedunits' => $completedunits,
        'totalunits' => $totalunits,
    ];
}

/**
 * Determines whether a module should be counted.
 *
 * @param cm_info $cm Course module information.
 * @param array $settings Plugin settings used to count progress.
 * @return bool
 */
function local_courseprogresspro_should_count_cm(cm_info $cm, array $settings): bool {
    if (!$cm->visible) {
        return false;
    }

    if (in_array($cm->modname, ['label', 'attendance'], true)) {
        return false;
    }

    if (in_array($cm->modname, ['resource', 'url', 'page', 'book', 'folder'], true) && empty($settings['countresources'])) {
        return false;
    }

    return true;
}

/**
 * Returns whether the module is treated as a resource item.
 *
 * @param cm_info $cm
 * @return bool
 */
function local_courseprogresspro_is_resource_cm(cm_info $cm): bool {
    return in_array($cm->modname, ['resource', 'url', 'page', 'book', 'folder'], true);
}

/**
 * Returns whether the module is not yet visible to the user due to availability.
 *
 * @param cm_info $cm
 * @return bool
 */
function local_courseprogresspro_is_pending_visibility(cm_info $cm): bool {
    return !$cm->uservisible;
}

/**
 * Builds one pending timeline item.
 *
 * @param cm_info $cm
 * @param string $detail
 * @return array
 */
function local_courseprogresspro_build_pending_item(cm_info $cm, string $detail): array {
    $available = !local_courseprogresspro_is_pending_visibility($cm);
    $url = '';
    $linklabel = get_string('pendingopenactivity', 'local_courseprogresspro');
    if (!empty($cm->url)) {
        $url = $cm->url->out(false);
    } else if (!empty($cm->course) && isset($cm->sectionnum) && $cm->sectionnum !== null) {
        $url = (new moodle_url('/course/view.php', ['id' => (int)$cm->course], 'section-' . (int)$cm->sectionnum))->out(false);
        $linklabel = get_string('pendinggotosection', 'local_courseprogresspro');
    }

    $availabilityinfo = '';
    if (!$available && !empty($cm->availableinfo)) {
        $availabilityinfo = trim(html_to_text($cm->availableinfo, 0));
    }

    return [
        'name' => format_string($cm->name ?: $cm->modplural),
        'type' => format_string($cm->modplural),
        'modname' => clean_param($cm->modname, PARAM_ALPHANUMEXT),
        'available' => $available ? 1 : 0,
        'url' => $url,
        'linklabel' => $linklabel,
        'availabilityinfo' => $availabilityinfo,
        'detail' => $detail,
    ];
}

/**
 * Gets progress units for one course module.
 *
 * @param cm_info $cm
 * @param int $userid
 * @param completion_info $completioninfo
 * @param array $settings
 * @return array
 */
function local_courseprogresspro_get_cm_progress(
    cm_info $cm,
    int $userid,
    completion_info $completioninfo,
    array $settings
): array {
    if (
        local_courseprogresspro_is_resource_cm($cm) &&
        !empty($settings['resourcesrequirecompletion']) &&
        $completioninfo->is_enabled($cm) == COMPLETION_TRACKING_NONE
    ) {
        return [
            'completedunits' => 0,
            'totalunits' => 0,
            'pendingdetail' => [],
        ];
    }

    if ($cm->modname === 'quiz' && ($settings['quizmode'] ?? 'questions') === 'questions') {
        return local_courseprogresspro_get_quiz_progress($cm, $userid);
    }

    $completed = !local_courseprogresspro_is_pending_visibility($cm) &&
        local_courseprogresspro_is_cm_completed($cm, $userid, $completioninfo);
    $name = format_string($cm->name ?: $cm->modplural);
    $detail = get_string('pendingactivityitem', 'local_courseprogresspro', (object)['name' => $name]);

    return [
        'completedunits' => $completed ? 1 : 0,
        'totalunits' => 1,
        'pendingdetail' => $completed ? [] : local_courseprogresspro_build_pending_item($cm, $detail),
    ];
}

/**
 * Gets quiz progress using the question count as total units.
 *
 * @param cm_info $cm
 * @param int $userid
 * @return array
 */
function local_courseprogresspro_get_quiz_progress(cm_info $cm, int $userid): array {
    global $DB;

    $totalquestions = (int)$DB->count_records('quiz_slots', ['quizid' => (int)$cm->instance]);
    $totalunits = max(1, $totalquestions);
    $completedunits = 0;
    $name = format_string($cm->name ?: 'Quiz');

    if (local_courseprogresspro_is_pending_visibility($cm)) {
        return [
            'completedunits' => 0,
            'totalunits' => $totalunits,
            'pendingdetail' => local_courseprogresspro_build_pending_item(
                $cm,
                get_string('pendingquizitem', 'local_courseprogresspro', (object)[
                    'name' => $name,
                    'count' => $totalunits,
                ])
            ),
        ];
    }

    $attempts = $DB->get_records(
        'quiz_attempts',
        ['quiz' => (int)$cm->instance, 'userid' => $userid],
        'timemodified DESC, attempt DESC',
        'id,uniqueid',
        0,
        1
    );
    $attempt = $attempts ? reset($attempts) : false;

    if ($attempt && !empty($attempt->uniqueid)) {
        $sql = "SELECT COUNT(DISTINCT qa.slot)
                  FROM {question_attempts} qa
                 WHERE qa.questionusageid = :usageid
                   AND qa.responsesummary IS NOT NULL
                   AND " . $DB->sql_compare_text('qa.responsesummary') . " <> :emptytext";
        $completedunits = (int)$DB->count_records_sql($sql, [
            'usageid' => (int)$attempt->uniqueid,
            'emptytext' => '',
        ]);
    }

    return [
        'completedunits' => max(0, min($totalunits, $completedunits)),
        'totalunits' => $totalunits,
        'pendingdetail' => $completedunits >= $totalunits ? [] : local_courseprogresspro_build_pending_item(
            $cm,
            get_string('pendingquizitem', 'local_courseprogresspro', (object)[
                'name' => $name,
                'count' => ($totalunits - $completedunits),
            ])
        ),
    ];
}

/**
 * Determines whether a non-quiz course module can be treated as completed.
 *
 * @param cm_info $cm
 * @param int $userid
 * @param completion_info $completioninfo
 * @return bool
 */
function local_courseprogresspro_is_cm_completed(
    cm_info $cm,
    int $userid,
    completion_info $completioninfo
): bool {
    global $DB;

    if (local_courseprogresspro_is_pending_visibility($cm)) {
        return false;
    }

    if ($completioninfo->is_enabled($cm) != COMPLETION_TRACKING_NONE) {
        $data = $completioninfo->get_data($cm, true, $userid);
        if (!empty($data->completionstate) && (int)$data->completionstate !== COMPLETION_INCOMPLETE) {
            return true;
        }
    }

    switch ($cm->modname) {
        case 'assign':
            return $DB->record_exists_select(
                'assign_submission',
                'assignment = :assignment AND userid = :userid AND status = :status',
                ['assignment' => (int)$cm->instance, 'userid' => $userid, 'status' => 'submitted']
            );

        case 'choice':
            return $DB->record_exists('choice_answers', ['choiceid' => (int)$cm->instance, 'userid' => $userid]);

        case 'feedback':
            return $DB->record_exists('feedback_completed', ['feedback' => (int)$cm->instance, 'userid' => $userid]);

        case 'forum':
            return $DB->record_exists_sql(
                "SELECT 1
                   FROM {forum_posts} fp
                   JOIN {forum_discussions} fd ON fd.id = fp.discussion
                  WHERE fd.forum = :forumid
                    AND fp.userid = :userid",
                ['forumid' => (int)$cm->instance, 'userid' => $userid]
            );

        case 'glossary':
            return $DB->record_exists('glossary_entries', ['glossaryid' => (int)$cm->instance, 'userid' => $userid]);

        case 'lesson':
            return $DB->record_exists('lesson_attempts', ['lessonid' => (int)$cm->instance, 'userid' => $userid]);

        case 'data':
            return $DB->record_exists('data_records', ['dataid' => (int)$cm->instance, 'userid' => $userid]);

        case 'h5pactivity':
            return $DB->record_exists('h5pactivity_attempts', ['h5pactivityid' => (int)$cm->instance, 'userid' => $userid]);

        case 'scorm':
            return $DB->record_exists('scorm_scoes_track', ['scormid' => (int)$cm->instance, 'userid' => $userid]);

        case 'workshop':
            return $DB->record_exists('workshop_submissions', ['workshopid' => (int)$cm->instance, 'authorid' => $userid]);

        case 'resource':
        case 'url':
        case 'page':
        case 'book':
        case 'folder':
            return false;
    }

    return false;
}

/**
 * Queue required assets.
 *
 * @param stdClass $course
 * @return void
 */
function local_courseprogresspro_bootstrap(stdClass $course): void {
    global $PAGE, $USER;

    static $loaded = false;
    if ($loaded || empty($USER->id) || isguestuser() || !(int)local_courseprogresspro_get_setting('enabled', 1)) {
        return;
    }
    $loaded = true;

    $snapshot = local_courseprogresspro_get_snapshot($course, (int)$USER->id);
    $showpercentage = (int)local_courseprogresspro_get_setting('showpercentage', 1);
    $showpendingbutton = (int)local_courseprogresspro_get_setting('showpendingbutton', 1);
    $headertext = (string)local_courseprogresspro_get_setting('headertext', get_string('progresslabel', 'local_courseprogresspro'));
    $pendingsummary = $snapshot['pendingcount'] > 0
        ? get_string('pendingcountsummary', 'local_courseprogresspro', $snapshot['pendingcount'])
        : get_string('allactivitiesdone', 'local_courseprogresspro');
    $pendingbuttonlabel = $snapshot['pendingcount'] > 0
        ? $pendingsummary
        : get_string('pendingbuttonlabel', 'local_courseprogresspro');

    $PAGE->requires->js_call_amd('local_courseprogresspro/progress', 'init', [[
        'label' => $headertext,
        'value' => $snapshot['percentage'],
        'pendingsummary' => $pendingsummary,
        'maxlabel' => '100%',
        'showpercentage' => $showpercentage,
        'showpendingbutton' => $showpendingbutton,
        'pendingbuttonlabel' => $pendingbuttonlabel,
        'pendingtitle' => get_string('pendingmodaltitle', 'local_courseprogresspro'),
        'pendingempty' => get_string('nopendingitems', 'local_courseprogresspro'),
        'closemodal' => get_string('closemodal', 'local_courseprogresspro'),
        'pendingstatusavailable' => get_string('pendingstatusavailable', 'local_courseprogresspro'),
        'pendingstatuslocked' => get_string('pendingstatuslocked', 'local_courseprogresspro'),
        'pendingopenactivity' => get_string('pendingopenactivity', 'local_courseprogresspro'),
        'pendingitems' => array_values($snapshot['pendingitems']),
    ]]);
}

/**
 * Render progress container at top of body.
 *
 * @return string
 */
function local_courseprogresspro_before_standard_top_of_body_html(): string {
    global $PAGE;

    if (!local_courseprogresspro_should_render()) {
        return '';
    }

    local_courseprogresspro_bootstrap($PAGE->course);

    return '<div id="local-courseprogresspro" class="local-courseprogresspro" aria-live="polite"></div>';
}

/**
 * Fallback hook to always load assets in course pages.
 *
 * @param global_navigation $navigation
 * @param stdClass $course
 * @param context_course $context
 * @return void
 */
function local_courseprogresspro_extend_navigation_course($navigation, $course, $context): void {
    if (empty($course->id) || (int)$course->id === SITEID) {
        return;
    }
    if (!(int)local_courseprogresspro_get_setting('enabled', 1)) {
        return;
    }

    local_courseprogresspro_bootstrap($course);
}
