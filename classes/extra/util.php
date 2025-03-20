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
 *
 *
 * @package     local_edudashboard
 * @category    admin
 * @copyright   2025 edudigital <geral@edudigital-learn.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edudashboard\extra;

defined('MOODLE_INTERNAL') || die();

use stdClass;
use context_course;

global $CFG;
require_once($CFG->libdir . "/completionlib.php");
require_once($CFG->dirroot . '/grade/querylib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/filelib.php');





/**
 * Class to get some extras info in Moodle.
 *
 * @package    local_edudashboard
 * @copyright  2019 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

 */

/**
 * Utility class providing extra functionality for the edudashboard plugin.
 */
class util {
    /**
     * @var array Month names array
     */
    private static $dataarray = [
      '1' => 'janeiro',
      '2' => 'fevereiro',
      '3' => 'março',
      '4' => 'abril',
      '5' => 'maio',
      '6' => 'junho',
      '7' => 'julho',
      '8' => 'agosto',
      '9' => 'setembro',
      '10' => 'outubro',
      '11' => 'novembro',
      '12' => 'dezembro',
    ];

    /**
     * Combines data into an array format.
     *
     * @param array $data Input data to combine
     * @return array Combined data array
     */
    public static function combineddata($data): array {
        $combineddata = [];
        foreach ($data as $coursename => $hours) {
            $combineddata[] = ['name' => $coursename, 'timespent' => $hours];
        }
        return $combineddata;
    }

    /**
     * Gets grades for a user in a specific category.
     *
     * @param int $userid User ID
     * @param int $categoryid Category ID
     * @return stdClass Response object with grade information
     */
    public static function gradeoncategory($userid, $categoryid) {
        global $DB;

        $nota = 0.0;
        $count = 0;
        $max = 0;
        $response = new stdClass();
        $courseset = \gradereport_overview_external::get_course_grades($userid);

        foreach ($courseset['grades'] as $course) {
            $coursen = $DB->get_record('course', ['id' => intval($course['courseid'])], 'fullname,category');

            if ($coursen->category === $categoryid) {
                $val = floatval($course['rawgrade']);
                $response->grades[$userid . '-' . $course['courseid']] = $val;
                $nota += $val;
                if ($val >= $max) {
                    $max = $val;
                }
                $count++;
            }
        }

        $response->media = ($count === 0) ? 0 : $nota / $count;
        $response->maxgrade = $max;

        return $response;
    }

    /**
     * Generates a fast system report.
     *
     * @return stdClass Report data
     */
    public static function system_fast_report() {
        global $DB;

        $response = new stdClass();

        $globalenrollment = 0;

        $globalcompletion = 0;

        $category = $DB->get_records('course_categories', null, null, "id,name");

        foreach ($category as $key => $categoria) {

            $sum = 0;
            $max = 0;
            $conclusoes = 0;
            $countusers = 0;

            $courses = course_report::getsitecourses(['category' => intval($categoria->id)], false);
            foreach ($courses as $course) {
                $users = get_enrolled_users(context_course::instance(intval($course->id)),
                null, null, "u.id,u.firstname, u.lastname");
                foreach ($users as $user) {

                    if (intval($user->id) !== 1) {

                        $completion = new \completion_info($course);

                        // First, let's make sure completion is enabled.
                        if ($completion->is_enabled()) {
                              $course->completed = $completion->is_course_complete($user->id);
                            if ($course->completed) {
                                $conclusoes += 1;
                            }
                        }

                         $countusers += 1;
                    }

                }

            }

              $globalenrollment += $countusers;

              $globalcompletion += $conclusoes;
        }
              $response->enrollments = $globalenrollment;

              $response->completions = $globalcompletion;

              set_config('coursesreport_sitecompletion', json_encode($response), 'local_edudashboard');

             return $response;
    }

    /**
     * Generates a fast admin report.
     *
     * @return stdClass Report data
     */
    public static function admin_fast_report() {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/user/lib.php');

        $response = new stdClass();

        $response->more_information = get_string('more_information', 'local_edudashboard');

        $usercount = $DB->count_records("user", ["suspended" => 0, "deleted" => 0]);
        $response->users = $usercount;
        $response->suspendedusers = $DB->count_records("user", ["suspended" => 1]);
        $response->active_suspend_users = get_string('active_suspend_users', 'local_edudashboard');

        $response->courses = $DB->count_records("course") - 1;
        $response->courses_label = get_string('courses', 'local_edudashboard');

        $data = json_decode(get_config('local_edudashboard', 'coursesreport_sitecompletion'), false);
        $fastreport = $data;

        if ($fastreport && isset($fastreport->completions) && isset($fastreport->enrollments)) {
            $response->completions = $fastreport->completions;
            $response->enrollments = $fastreport->enrollments;
            $response->completionpercent = $fastreport->enrollments > 0
                ? round((100 * $fastreport->completions) / $fastreport->enrollments, 1)
                : 0;
        } else {
            $response->completions = 0;
            $response->enrollments = 0;
            $response->completionpercent = 0;
        }
        $response->courses_conclusio_label = get_string('courses_conclusion', 'local_edudashboard');

        $today = strtotime("today");
        $sql = "SELECT COUNT(id)
                FROM {user}
                WHERE lastaccess >= :todaysdate
                AND deleted = 0
                AND suspended = 0";
        $response->todaysusers = $DB->count_records_sql($sql, ["todaysdate" => $today]);
        $perpage = 100;
        $page = 0;
        $todaysusers = $DB->get_records_sql(
            "SELECT id, firstname, lastname, email, lastaccess
            FROM {user}
            WHERE lastaccess >= :todaysdate
            AND deleted = 0
            AND suspended = 0
            ORDER BY lastaccess DESC",
            ["todaysdate" => $today],
            $page * $perpage,
            $perpage
        );
        $response->todaysusers_array = array_values($todaysusers);
        $response->authentications_today_label = get_string('authentications_today', 'local_edudashboard');

        return $response;
    }
    /**
     * Calculates the size of system files.
     *
     * @return array Array containing total size and course sizes string
     */
    public static function getsystemfilessize() {
        global $DB;

        $filetimesmimetype = [
            'application/zip',
            'application/vnd.moodle.backup',
            'application/pdf',
            'image/jpeg',
            'image/png',
            'audio/mp3',
            'video/mp4',
        ];

        list($insql, $inparams) = $DB->get_in_or_equal($filetimesmimetype, SQL_PARAMS_QM, null);

        $sql = 'SELECT sum(filesize) as size FROM {files} WHERE status=0';
        $result = $DB->get_record_sql($sql, $inparams)->size;

        $courses = course_report::getsitecourses([], false);
        $strcrssize = '';

        foreach ($courses as $course) {
            $result1 = 0;
            $mds = get_course_mods($course->id);

            foreach ($mds as $mod) {
                $sql2 = 'SELECT * FROM {context} WHERE instanceid = :instanceid';
                $params = ['instanceid' => $mod->id];
                $records = $DB->get_records_sql($sql2, $params);

                $id = \context_module::instance($mod->id)->id;
                $sql = 'SELECT sum(filesize) as course_size FROM {files} WHERE contextid = :contextid AND status = 0';
                $params = ['contextid' => $id];
                $result1 += round($DB->get_record_sql($sql, $params)->course_size / (1024 * 1024), 2);
            }

            $strcrssize .= $course->id . '-' . $result1 . ';';
        }

        return [round($result / (1024 * 1024), 2), $strcrssize]; // In Megabyte.
    }
    /**
     * Checks if the system is running Totara.
     *
     * @return bool True if Totara is detected
     */
    public static function istotara() {
        global $CFG;
        return file_exists($CFG->dirroot . "/totara");
    }
    /**
     * Gets learners enrolled in a program.
     *
     * @param int $progid Program ID
     * @param bool|int $status Status filter (optional)
     * @return array Users enrolled in the program
     */
    public static function get_program_learners($progid, $status = false) {
        global $DB;
        if ($status !== false) {
            $statussql = 'AND status = ?';
            $statusparams = [(int) $status];
        } else {
            $statussql = '';
            $statusparams = [];
        }
        $sql = "SELECT id, firstname, lastname, email FROM {user} WHERE id IN
        (SELECT DISTINCT userid FROM {prog_completion}
        WHERE coursesetid = 0 AND programid = :programid {$statussql})";
        $params = array_merge(['programid' => $progid], $statusparams);
        $users = $DB->get_records_sql($sql, $params);

        $params = array_merge([$progid], $statusparams);

        return $DB->get_records_sql($sql, $params);
    }
    /**
     * Converts a timestamp to a formatted date string.
     *
     * @param int $date Unix timestamp
     * @param bool $showtime Whether to show time
     * @return string Formatted date string
     */
    public static function dateconverter(int $date, bool $showtime = true) {

        $data = date('d/m/Y H:i:s', $date);

        $sparar = explode(" ", $data);

        list($day, $month, $ano) = explode("/", $sparar[0]);

        list($hora, $min, $seg) = explode(":", $sparar[1]);

        if ($showtime) {
            return $day . "/" . $month . "/" . $ano . ", às " . $hora . ":" . $min;
        } else {
            return $day . "/" . $month . "/" . $ano;
        }

    }
    /**
     * Retrieves the list of courses enrolled by a specific user.
     *
     * @param int $userid The ID of the user whose courses are to be retrieved
     * @return array An array of courses with details such as ID, fullname, category, grade, and completion status
     */
    public static function mycourses($userid) {
        global $DB;
        $courses = [];
        $courseset = enrol_get_users_courses($userid, true, '*', 'visible DESC, fullname ASC, sortorder ASC');

        foreach ($courseset as $course) {
            $course1 = [];
            $course1['id'] = $course->id;
            $course1['fullname'] = $course->fullname;
            $course1['category'] = $DB->get_record('course_categories', ['id' => $course->category], "name")->name;
            $usergrade = \grade_get_course_grade($userid, $course->id);

            $grade = round($usergrade->grade, 2);
            $course1['finished'] = course_report::getuser_course_progress_percentage($userid, $course) == 100;

            $course1['rawgrade'] = $grade;
            $course1['maxgrade'] = round($usergrade->item->grademax, 2);
            $courses[] = $course1;
        }

        return $courses;
    }
}

