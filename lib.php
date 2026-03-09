<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

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
    $viewedcmids = local_courseprogresspro_get_viewed_cmids((int)$course->id, $userid);
    $settings = [
        'countresources' => (int)local_courseprogresspro_get_setting('countresources', 1),
        'quizmode' => (string)local_courseprogresspro_get_setting('quizmode', 'questions'),
    ];

    $completedunits = 0;
    $totalunits = 0;
    $pendingitems = [];

    foreach ($modinfo->get_cms() as $cm) {
        if (!local_courseprogresspro_should_count_cm($cm, $settings)) {
            continue;
        }

        $progress = local_courseprogresspro_get_cm_progress($cm, $userid, $completioninfo, $viewedcmids, $settings);
        $completedunits += $progress['completedunits'];
        $totalunits += $progress['totalunits'];

        if (!empty($progress['pendingdetail'])) {
            $pendingitems[] = $progress['pendingdetail'];
        }
    }

    $percentage = 0;
    if ($totalunits > 0) {
        $percentage = (int)round(($completedunits / $totalunits) * 100);
    }

    usort($pendingitems, static function(array $a, array $b): int {
        return ($b['available'] ?? 0) <=> ($a['available'] ?? 0);
    });

    return [
        'completedunits' => $completedunits,
        'totalunits' => $totalunits,
        'percentage' => max(0, min(100, $percentage)),
        'pendingitems' => $pendingitems,
    ];
}

/**
 * Determines whether a module should be counted.
 *
 * @param cm_info $cm
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
    if ($available && !empty($cm->url)) {
        $url = $cm->url->out(false);
    }

    return [
        'name' => format_string($cm->name ?: $cm->modplural),
        'type' => format_string($cm->modplural),
        'modname' => clean_param($cm->modname, PARAM_ALPHANUMEXT),
        'available' => $available ? 1 : 0,
        'url' => $url,
        'detail' => $detail,
    ];
}

/**
 * Gets progress units for one course module.
 *
 * @param cm_info $cm
 * @param int $userid
 * @param completion_info $completioninfo
 * @param array $viewedcmids
 * @return array
 */
function local_courseprogresspro_get_cm_progress(
    cm_info $cm,
    int $userid,
    completion_info $completioninfo,
    array $viewedcmids,
    array $settings
): array {
    if ($cm->modname === 'quiz' && ($settings['quizmode'] ?? 'questions') === 'questions') {
        return local_courseprogresspro_get_quiz_progress($cm, $userid);
    }

    $completed = !local_courseprogresspro_is_pending_visibility($cm) &&
        local_courseprogresspro_is_cm_completed($cm, $userid, $completioninfo, $viewedcmids);
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
 * @param array $viewedcmids
 * @return bool
 */
function local_courseprogresspro_is_cm_completed(
    cm_info $cm,
    int $userid,
    completion_info $completioninfo,
    array $viewedcmids
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
            return in_array((int)$cm->id, $viewedcmids, true);
    }

    return in_array((int)$cm->id, $viewedcmids, true);
}

/**
 * Gets course module ids viewed by the user from standard logs.
 *
 * @param int $courseid
 * @param int $userid
 * @return array
 */
function local_courseprogresspro_get_viewed_cmids(int $courseid, int $userid): array {
    global $DB;

    if (!$DB->get_manager()->table_exists('logstore_standard_log')) {
        return [];
    }

    $records = $DB->get_records_sql(
        "SELECT DISTINCT contextinstanceid
           FROM {logstore_standard_log}
          WHERE courseid = :courseid
            AND userid = :userid
            AND contextlevel = :contextlevel
            AND action = :action",
        [
            'courseid' => $courseid,
            'userid' => $userid,
            'contextlevel' => CONTEXT_MODULE,
            'action' => 'viewed',
        ]
    );

    return array_map('intval', array_keys($records));
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
    $showcompletedcount = (int)local_courseprogresspro_get_setting('showcompletedcount', 1);
    $showpendingbutton = (int)local_courseprogresspro_get_setting('showpendingbutton', 1);
    $headertext = (string)local_courseprogresspro_get_setting('headertext', get_string('progresslabel', 'local_courseprogresspro'));

    $PAGE->requires->css('/local/courseprogresspro/styles.css');
    $PAGE->requires->js_call_amd('local_courseprogresspro/progress', 'init', [[
        'label' => $headertext,
        'value' => $snapshot['percentage'],
        'completedcount' => get_string('completedcount', 'local_courseprogresspro', (object)[
            'completed' => $snapshot['completedunits'],
            'total' => $snapshot['totalunits'],
        ]),
        'maxlabel' => '100%',
        'showpercentage' => $showpercentage,
        'showcompletedcount' => $showcompletedcount,
        'showpendingbutton' => $showpendingbutton,
        'pendingbuttonlabel' => get_string('pendingbuttonlabel', 'local_courseprogresspro'),
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
