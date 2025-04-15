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
 * @package      local_edudashboard
 * @copyright   2025 edudigital <geral@edudigital-learn.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edudashboard\extra;

defined('MOODLE_INTERNAL') || die();

use context_course;

global $CFG;
require_once($CFG->libdir . "/completionlib.php");
require_once($CFG->dirroot . '/grade/querylib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/enrol/locallib.php');
require_once($CFG->libdir . '/gradelib.php');

/**
 * Class to get some extras info in Moodle.
 *
 * @package    local_edudashboard
 * @copyright  2019 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class course_report
 */
class course_report {

    /**
     * Retrieves the file sizes of courses from cache.
     *
     * @return array|string Array of course sizes (courseid => size) or error message if cache fails
     */
    public static function getcoursefilessize() {
        $coursessize = [];
        try {
            $cache = \cache::make('local_edudashboard', 'admininfos');
        } catch (\coding_exception $e) {
            return "Error. Need Settings 'admininfos'.";
        }

        $data = $cache->get('coursesdiskusage');

        if (!is_string($data) || empty($data)) {
            return $coursessize;
        }

        $data = explode(";", $data);

        foreach ($data as $dat) {
            if (empty($dat)) {
                continue;
            }

            $arra = explode("-", $dat);

            if (count($arra) >= 2 && isset($arra[0]) && isset($arra[1])) {
                $courseid = intval($arra[0]);
                $size = $arra[1];

                if (is_numeric($size)) {
                    $coursessize[$courseid] = $size;
                }
            }
        }

        return $coursessize;
    }

    /**
     * Retrieves site-wide course completion statistics.
     *
     * @return array|null [] contain course [], global enrolments, compl. percentage, compl. count, tot. size; or null if no courses
     */
    public static function getsitecoursescompletion() {
        $courses = self::getsitecourses([], false);
        $coursessize = self::getcoursefilessize();

        $globalenrrolments = 0;
        $globalcoursessize = 0;
        $globalcompleted = 0;
        $coursearray = [];

        if (!$courses) {
            return null;
        }

        foreach ($courses as $course) {
            if ($course->id == 1) {
                continue;
            }

            $course->size_f = isset($coursessize[$course->id]) ? self::datasizeformater($course->size) : get_string
            ('coursessizeC', 'local_edudashboard');
            $userpicked = get_enrolled_users(context_course::instance(intval($course->id)), null, null,
                "u.id,u.firstname,u.email, u.lastname", "u.firstname ASC");

            $countusers = 0;
            $completedusers = 0;
            foreach ($userpicked as $user) {
                if (intval($user->id) !== 1) {
                    if ((new \completion_info($course))->is_course_complete($user->id)) {
                        $completedusers++;
                    }
                    $countusers += 1;
                }
            }

            $course->total_enrolled = $countusers;
            $globalenrrolments += $course->total_enrolled;
            $course->completedusers = $completedusers;
            $globalcompleted += $course->completedusers;
            $course->completedusers_percentage = $countusers !== 0 ? round(100 * $completedusers / $countusers, 2) : 0;
            $coursearray[] = $course;
        }
        return [$coursearray, $globalenrrolments, $globalenrrolments != 0 ?
            round((100 * $globalcompleted) / $globalenrrolments, 1) : 0, $globalcompleted,
            self::datasizeformater($globalcoursessize)];
    }

    /**
     * Formats a disk usage size into a human-readable string with appropriate units.
     *
     * @param float|int $diskusage Disk usage size in MB
     * @return string Formatted size with unit (e.g., "123.45 MB" or "1.23 GB")
     */
    public static function datasizeformater($diskusage) {
        $usageunit = ' MB';

        if ($diskusage >= 1024) {
            $diskusage = round($diskusage / 1024, 2);
            $usageunit = ' GB';
        }

        return $diskusage . $usageunit;
    }

    /**
     * Retrieves all site courses based on selection criteria.
     *
     * @param array $select Conditions to filter courses (e.g., ['id' => 1])
     * @param bool $forcegethiddencurses If true, includes hidden courses; otherwise, only visible courses
     * @return array Array of course objects
     */
    public static function getsitecourses(array $select, bool $forcegethiddencurses) {
        global $DB;
        if (!$forcegethiddencurses) {
            $select["visible"] = 1;
        }

        $courses = $DB->get_records("course", $select, "fullname ASC", '*');
        return $courses;
    }

    /**
     * Calculates a user's course progress percentage.
     *
     * @param int $userid The ID of the user
     * @param stdClass $course The course object
     * @return float|int Progress percentage rounded to 2 decimals, or 0 if no progress
     */
    public static function getuser_course_progress_percentage($userid, $course) {
        global $CFG;

        $istotara = false;

        if (is_dir($CFG->dirroot . "/totara")) {
            $istotara = true;
        }

        if ($istotara) {
            $completion = new completion_completion(['userid' => $userid, 'course' => $course->id]);
            $progressinfo = $completion->get_progressinfo();
            $percent = $progressinfo->get_percentagecomplete();
            return $percent ? round($percent, 2) : 0;
        } else {
            $progressprct = \core_completion\progress::get_course_progress_percentage($course, $userid);
            return $progressprct ? round($progressprct, 2) : 0;
        }
    }
}
