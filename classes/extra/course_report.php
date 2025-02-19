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

class course_report
{


    public static function getCoursesFilesSize()
    {


        $courses_size = [];

        try {
            $cache = \cache::make('local_edudashboard', 'admininfos');
        } catch (\coding_exception $e) {
            return "Erro. Possivelmente a Cahche para 'admininfos' não foi configurado propriamente.";
        }

        $data = $cache->get('coursesdiskusage');

        $data = explode(";", $data);

        foreach ($data as $dat) {
            $arra = explode("-", $dat);

            $courses_size[intval($arra[0])] = $arra[1];
        }

        return $courses_size;
    }


   

    public static function getSitecoursesCompletion()
    {

        $courses = self::getSiteCourses(array(), false);

        $courses_size = course_report::getCoursesFilesSize();

        $global_enrrolments = 0;

        $global_courses_size = 0;

        $global_completed = 0;
        $coursearray = [];

        if (!$courses) {
            return;
        }

        foreach ($courses as $course) {

            if ($course->id == 1) {
                continue;
            }

            $course->size = doubleval($courses_size[$course->id]);

            $global_courses_size += $course->size;

            $course->size_f = isset($courses_size[$course->id]) ? course_report::dataSizeFormater($course->size) : "Não calculado"; //Formatted Size: for output purpouse;

            $userpicked = get_enrolled_users(context_course::instance(intval($course->id)), null, null, "u.id,u.firstname,u.email, u.lastname", "u.firstname ASC");


            $count_users = 0;

            $completedusers = 0;

            foreach ($userpicked as $user) {

                if (intval($user->id) !== 1) {

                    if ((new \completion_info($course))->is_course_complete($user->id)) {
                        $completedusers++;
                    }

                    $count_users += 1;
                }
            }

            $course->total_enrolled = $count_users;

            $global_enrrolments += $course->total_enrolled;

            $course->completedusers = $completedusers;

            $global_completed += $course->completedusers;

            $course->completedusers_percentage = $count_users !== 0 ? round(100 * $completedusers / $count_users, 2) : 0;

            $coursearray[] = $course;
        }


        return array($coursearray, $global_enrrolments, $global_enrrolments!=0?round((100 * $global_completed) / $global_enrrolments, 1):0, $global_completed, course_report::dataSizeFormater($global_courses_size));
    }
    public static function dataSizeFormater($diskusage)
    {

        $usageunit = ' MB';

        if ($diskusage >= 1024) {

            $diskusage = round($diskusage / 1024, 2);

            $usageunit = ' GB';
        }

        return $diskusage . $usageunit;
    }


  

    public static function getSiteCourses(array $select, bool $forceGetHiddenCurses)
    {
        global $DB;
        $select["visible"] = 1;

        $courses = $DB->get_records("course", $select, "fullname ASC", '*');

        return $courses;
    }

}
