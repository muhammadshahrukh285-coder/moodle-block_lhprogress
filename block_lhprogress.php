<?php

defined('MOODLE_INTERNAL') || die();

class block_lhprogress extends block_base {

    public function init(): void {
        $this->title = get_string('pluginname', 'block_lhprogress');
    }

    public function has_config(): bool {
        return true;
    }

    public function get_content() {
        global $PAGE, $OUTPUT, $USER, $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();

        $PAGE->requires->css(
            new moodle_url('/blocks/lhprogress/styles.css')
        );

        require_login();

        /*
         * ============================================================
         * ADMIN SETTINGS
         * ============================================================
         */

        $coursecategory = (int) get_config(
            'block_lhprogress',
            'coursecategory'
        );

        $includesubcategories = (bool) get_config(
            'block_lhprogress',
            'includesubcategories'
        );

        $enableduedates = (bool) get_config(
            'block_lhprogress',
            'enableduedates'
        );

        $dueafter = max(0, (int) get_config(
            'block_lhprogress',
            'dueafter'
        ));

        $overdueafter = max(0, (int) get_config(
            'block_lhprogress',
            'overdueafter'
        ));

        $overduegrace = max(0, (int) get_config(
            'block_lhprogress',
            'overduegrace'
        ));

        $showcompleted = (bool) get_config(
            'block_lhprogress',
            'showcompleted'
        );

        $showinprogress = (bool) get_config(
            'block_lhprogress',
            'showinprogress'
        );

        $shownotstarted = (bool) get_config(
            'block_lhprogress',
            'shownotstarted'
        );

        $showduesoon = (bool) get_config(
            'block_lhprogress',
            'showduesoon'
        );

        $showoverdue = (bool) get_config(
            'block_lhprogress',
            'showoverdue'
        );

        $showenrollmentdate = (bool) get_config(
            'block_lhprogress',
            'showenrollmentdate'
        );

        $showduedate = (bool) get_config(
            'block_lhprogress',
            'showduedate'
        );

        $showprogress = (bool) get_config(
            'block_lhprogress',
            'showprogress'
        );

        $showbrowsecourses = (bool) get_config(
            'block_lhprogress',
            'showbrowsecourses'
        );

        /*
         * ============================================================
         * COURSE QUERY
         * ============================================================
         */

        $params = [
            'userid' => $USER->id,
            'userstatus' => ENROL_USER_ACTIVE,
            'enrolstatus' => ENROL_INSTANCE_ENABLED,
            'siteid' => SITEID,
        ];

        $categorywhere = '';

        if ($coursecategory > 0) {

            $category = core_course_category::get(
                $coursecategory,
                IGNORE_MISSING
            );

            if ($category) {

                if ($includesubcategories) {

                    $categorywhere = ' AND cc.path LIKE :categorypath ';

                    $params['categorypath'] = $category->path . '/%';

                } else {

                    $categorywhere = ' AND cc.id = :categoryid ';

                    $params['categoryid'] = $coursecategory;
                }
            }
        }

        $sql = "SELECT
                    c.id,
                    c.fullname,
                    c.shortname,
                    c.visible,
                    c.enddate,
                    MIN(
                        CASE
                            WHEN ue.timestart > 0 THEN ue.timestart
                            ELSE ue.timecreated
                        END
                    ) AS enrollmentdate

                FROM {course} c

                JOIN {course_categories} cc
                    ON cc.id = c.category

                JOIN {enrol} e
                    ON e.courseid = c.id
                    AND e.status = :enrolstatus

                JOIN {user_enrolments} ue
                    ON ue.enrolid = e.id
                    AND ue.userid = :userid
                    AND ue.status = :userstatus

                WHERE c.id <> :siteid
                AND c.visible = 1
                {$categorywhere}

                GROUP BY
                    c.id,
                    c.fullname,
                    c.shortname,
                    c.visible,
                    c.enddate

                ORDER BY c.fullname ASC";

        $enrolledcourses = $DB->get_records_sql(
            $sql,
            $params
        );

        /*
         * ============================================================
         * INITIAL VALUES
         * ============================================================
         */

        $courses = [];

        $completedcourses = 0;
        $duesooncourses = 0;
        $overduecourses = 0;

        $totalprogress = 0;
        $progresscoursecount = 0;

        $now = time();

        /*
         * ============================================================
         * PROCESS COURSES
         * ============================================================
         */

        foreach ($enrolledcourses as $course) {

            $enrollmentdate = (int) $course->enrollmentdate;

            $name = format_string($course->fullname);

            $initial = strtoupper(
                core_text::substr(
                    trim($name),
                    0,
                    1
                )
            );

            /*
             * Moodle completion progress.
             */

            $progress = \core_completion\progress::get_course_progress_percentage(
                $course,
                $USER->id
            );

            if ($progress === null) {
                $progress = 0;
            }

            $progress = (int) round($progress);

            /*
             * ========================================================
             * DEFAULT TIME VALUES
             * ========================================================
             */

            $duedate = 0;
            $overduethreshold = 0;
            $finaloverduedate = 0;

	    $timeprogress = 0;
	    $dueposition = 0;
	    $overdueposition = 0;
	    $finaloverdueposition = 100;
	    $daysremaining = 0;
	    $timeremainingtext = '';

            $showtimetracking = false;

            $duesoon = false;
            $due = false;
            $graceperiod = false;
            $overdue = false;

            $timeclass = 'normal';

            /*
             * ========================================================
             * COURSE STATUS
             * ========================================================
             */

            if ($progress >= 100) {

                /*
                 * Completed always takes priority.
                 */
                $status = get_string(
                    'completed',
                    'block_lhprogress'
                );

                $statusclass = 'completed';

                $completedcourses++;

            } else {

                /*
                 * ====================================================
                 * TIME TRACKING
                 * ====================================================
                 */

                if ($enableduedates && $enrollmentdate > 0) {

                    $showtimetracking = true;

                    /*
                     * All calculations are based on the individual
                     * user's enrollment date.
                     */

                    $duedate = $enrollmentdate + (
                        $dueafter * DAYSECS
                    );

                    $overduethreshold = $enrollmentdate + (
                        $overdueafter * DAYSECS
                    );

                    $finaloverduedate = $overduethreshold + (
                        $overduegrace * DAYSECS
                    );

                    /*
                     * Overall time journey:
                     *
                     * Enrollment → Final Overdue
                     */

$totaltime = $finaloverduedate - $enrollmentdate;

if ($totaltime > 0) {

    /*
     * Current position on the complete timeline.
     */
    $elapsedtime = $now - $enrollmentdate;

    $timeprogress = (int) round(
        ($elapsedtime / $totaltime) * 100
    );

    $timeprogress = max(
        0,
        min(100, $timeprogress)
    );

    /*
     * Timeline milestone positions.
     *
     * Example:
     * Due after 14 days.
     * Overdue after 30 days.
     * Grace period 7 days.
     *
     * Final timeline = 37 days.
     */

    $dueposition = (int) round(
        (($duedate - $enrollmentdate) / $totaltime) * 100
    );

    $overdueposition = (int) round(
        (($overduethreshold - $enrollmentdate) / $totaltime) * 100
    );

    $dueposition = max(
        0,
        min(100, $dueposition)
    );

    $overdueposition = max(
        0,
        min(100, $overdueposition)
    );
}

                    /*
                     * ----------------------------------------------
                     * FINAL OVERDUE
                     * ----------------------------------------------
                     */

                    if ($now >= $finaloverduedate) {

                        $overdue = true;
                        $timeclass = 'overdue';

                        $status = get_string(
                            'overdue',
                            'block_lhprogress'
                        );

                        $statusclass = 'overdue';

                        $overduecourses++;

                    /*
                     * ----------------------------------------------
                     * GRACE PERIOD
                     * ----------------------------------------------
                     */

                    } elseif (
                        $overduegrace > 0 &&
                        $now >= $overduethreshold
                    ) {

                        $graceperiod = true;
                        $timeclass = 'grace';

                        /*
                         * Keep the course academically in progress,
                         * but make the timeline show Grace Period.
                         */

                        if ($progress > 0) {

                            $status = get_string(
                                'inprogress',
                                'block_lhprogress'
                            );

                            $statusclass = 'inprogress';

                        } else {

                            $status = get_string(
                                'notstarted',
                                'block_lhprogress'
                            );

                            $statusclass = 'notstarted';
                        }

                    /*
                     * ----------------------------------------------
                     * DUE PERIOD
                     * ----------------------------------------------
                     */

                    } elseif ($now >= $duedate) {

                        $due = true;
                        $timeclass = 'due';

                        if ($progress > 0) {

                            $status = get_string(
                                'inprogress',
                                'block_lhprogress'
                            );

                            $statusclass = 'inprogress';

                        } else {

                            $status = get_string(
                                'notstarted',
                                'block_lhprogress'
                            );

                            $statusclass = 'notstarted';
                        }

                    /*
                     * ----------------------------------------------
                     * DUE SOON
                     * ----------------------------------------------
                     */

                    } elseif (
                        $duedate <= $now + (7 * DAYSECS)
                    ) {

                        $duesoon = true;
                        $timeclass = 'duesoon';

                        $status = get_string(
                            'duesoon',
                            'block_lhprogress'
                        );

                        $statusclass = 'duesoon';

                        $duesooncourses++;

                    /*
                     * ----------------------------------------------
                     * NORMAL
                     * ----------------------------------------------
                     */

                    } elseif ($progress > 0) {

                        $status = get_string(
                            'inprogress',
                            'block_lhprogress'
                        );

                        $statusclass = 'inprogress';

                    } else {

                        $status = get_string(
                            'notstarted',
                            'block_lhprogress'
                        );

                        $statusclass = 'notstarted';
                    }

                } else {

                    /*
                     * Due/overdue tracking disabled.
                     */

                    if ($progress > 0) {

                        $status = get_string(
                            'inprogress',
                            'block_lhprogress'
                        );

                        $statusclass = 'inprogress';

                    } else {

                        $status = get_string(
                            'notstarted',
                            'block_lhprogress'
                        );

                        $statusclass = 'notstarted';
                    }
                }
            }

            /*
             * ========================================================
             * STATUS DISPLAY FILTERS
             * ========================================================
             */

            if (
                ($statusclass === 'completed' && !$showcompleted) ||
                ($statusclass === 'inprogress' && !$showinprogress) ||
                ($statusclass === 'notstarted' && !$shownotstarted) ||
                ($statusclass === 'duesoon' && !$showduesoon) ||
                ($statusclass === 'overdue' && !$showoverdue)
            ) {
                continue;
            }

            /*
             * ========================================================
             * DAYS REMAINING
             * ========================================================
             */

            if ($showtimetracking && $progress < 100) {

                if ($now < $duedate) {

                    $daysremaining = (int) ceil(
                        ($duedate - $now) / DAYSECS
                    );

                } elseif ($graceperiod) {

                    $daysremaining = (int) ceil(
                        ($finaloverduedate - $now) / DAYSECS
                    );

                } elseif ($overdue) {

                    $daysremaining = 0;
                }
            }

            /*
             * ========================================================
             * OVERALL PROGRESS
             * ========================================================
             */

            $totalprogress += $progress;
            $progresscoursecount++;

            /*
             * ========================================================
             * COURSE DATA
             * ========================================================
             */

            $courses[] = [

                'name' => $name,

                'initial' => $initial,

                'progress' => $progress,

                'showprogress' => $showprogress,

                'status' => $status,

                'statusclass' => $statusclass,

                /*
                 * Enrollment date.
                 */

                'showenrollmentdate' => $showenrollmentdate,

                'enrollmentdate' => (
                    $showenrollmentdate &&
                    $enrollmentdate
                )
                    ? userdate(
                        $enrollmentdate,
                        get_string(
                            'strftimedate',
                            'langconfig'
                        )
                    )
                    : '',

                /*
                 * Due date.
                 */

                'showduedate' => (
                    $showduedate &&
                    $enableduedates &&
                    $duedate > 0
                ),

                'duedate' => (
                    $showduedate &&
                    $enableduedates &&
                    $duedate > 0
                )
                    ? userdate(
                        $duedate,
                        get_string(
                            'strftimedate',
                            'langconfig'
                        )
                    )
                    : '',

                /*
                 * Time tracking.
                 *
                 * IMPORTANT:
                 * Completed courses do not receive the time bar.
                 */

                'showtimetracking' => (
                    $showtimetracking &&
                    $progress < 100
                ),

                'timeprogress' => $timeprogress,

'dueposition' => $dueposition,

'overdueposition' => $overdueposition,

'finaloverdueposition' => $finaloverdueposition,

                'daysremaining' => $daysremaining,

                'duesoon' => $duesoon,

                'due' => $due,

                'graceperiod' => $graceperiod,

                'overdue' => $overdue,

                'timeclass' => $timeclass,

                /*
                 * Course URL.
                 */

                'url' => new moodle_url(
                    '/course/view.php',
                    [
                        'id' => $course->id
                    ]
                ),
            ];
        }

        /*
         * ============================================================
         * OVERALL LEARNING PROGRESS
         * ============================================================
         */

        if ($progresscoursecount > 0) {

            $overallprogress = (int) round(
                $totalprogress / $progresscoursecount
            );

        } else {

            $overallprogress = 0;
        }

        /*
         * ============================================================
         * SVG RING
         * ============================================================
         */

        $circumference = 314.16;

        $progressoffset = $circumference
            - (
                ($overallprogress / 100)
                * $circumference
            );

        /*
         * ============================================================
         * RENDER
         * ============================================================
         */

        $this->content->text = $OUTPUT->render_from_template(
            'block_lhprogress/progress',
            [

                'overallprogress' => $overallprogress,

                'progressoffset' => $progressoffset,

                'activecourses' => max(
                    0,
                    count($courses) - $completedcourses
                ),

                'completedcourses' => $completedcourses,

'showduesoonstat' => (
    $enableduedates &&
    $showduesoon
),

'duesooncourses' => $duesooncourses,

'showoverduestat' => (
    $enableduedates &&
    $showoverdue
),

'overduecourses' => $overduecourses,

                'showbrowsecourses' => $showbrowsecourses,

                'viewallurl' => new moodle_url(
                    '/my/courses.php'
                ),

                'coursesurl' => new moodle_url(
                    '/my/courses.php'
                ),

                'courses' => $courses,
            ]
        );

        $this->content->footer = '';

        return $this->content;
    }

    public function applicable_formats(): array {
        return [
            'my' => true,
            'site-index' => false,
        ];
    }
}
