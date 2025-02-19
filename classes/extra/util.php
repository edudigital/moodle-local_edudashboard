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

class util
{
  public static function combined_data($data): array
  {
    $combined_data = [];
    foreach ($data as $course_name => $hours) {
      $combined_data[] = ['name' => $course_name, 'timespent' => $hours];
    }
    return $combined_data;
  }

  static $DATA_ARRAY = [
    '1' => "janeiro",
    '2' => "fevereiro",
    '3' => "março",
    '4' => "abril",
    '5' => "maio",
    '6' => "junho",
    '7' => "julho",
    '8' => "agosto",
    '9' => "setembro",
    '10' => "outubro",
    '11' => "novembro",
    '12' => "dezembro"

  ];
  public static function mycourses($user_id)
  {
    global $DB;
    $courses = [];

    $courseset = enrol_get_users_courses($user_id, true, '*', 'visible DESC, fullname ASC, sortorder ASC');

    foreach ($courseset as $course) {


      $course_1 = [];
      $course_1['id'] = $course->id;
      $course_1['fullname'] = $course->fullname;
      $course_1['category'] = $DB->get_record('course_categories', ['id' => $course->category], "name")->name;
      $usergrade = \grade_get_course_grade($user_id, $course->id);

      $grade = round($usergrade->grade, 2);
      $course_1['finished'] = course_report::getuser_course_progress_percentage($user_id, $course) == 100;

      $course_1['rawgrade'] = $grade;
      $course_1['maxgrade'] = round($usergrade->item->grademax, 2);
      $courses[] = $course_1;
    }

    return $courses;
  }

  public static function grade_oncategory($user_id, $category_id)
  {
    global $DB;
    $nota = 0.0;
    $count = 0;
    $max = 0;
    $response = new stdClass();
    $courseset = \gradereport_overview_external::get_course_grades($user_id);

    foreach ($courseset['grades'] as $course) {
 
      $course_n = $DB->get_record('course', ['id' => intval($course['courseid'])], "fullname,category");

      if ($course_n->category === $category_id) {
        $val = floatval($course['rawgrade']);
        $response->grades[$user_id . "-" . $course['courseid']] = $val;
        $nota += $val;
        if ($val >= $max) {
          $max = $val;
        }
        $count++;
      }

    }
    if ($count === 0) {
      $response->media = 0;
    } else {
      $response->media = $nota / $count;
    }
    $response->maxgrade = $max;

    return $response;
  }

  public static function categoria_fulldata()
  {
    //Mthod not in use
    global $DB, $USER;

    $show_hidden = get_config('local_edudashboard', 'show_hidden_categories'); // only cathegories

    $category = $DB->get_records('course_categories', $show_hidden == 0 ? ['visible' => 1, 'visibleold' => 1] : null, " name ASC", "id,visible,name");

    foreach ($category as $key => $categoria) {

      $sum = 0;
      $max = 0;
      $conclusoes = 0;
      $count_users = 0;

      $courses = course_report::getSiteCourses(array('category' => intval($categoria->id)), false);


      foreach ($courses as $course) {
        $useres = [];
        $users = get_enrolled_users(context_course::instance(intval($course->id)), null, null, "u.id,u.firstname, u.lastname", "u.firstname ASC");
        foreach ($users as $user) {

          if (intval($user->id) !== 1) {
       
            $usergrade = \grade_get_course_grade($user->id, $course->id);
            $grade = round($usergrade->grade, 2);

            $sum += $grade;

            $user->grade = $grade;



            if ($grade >= $max) {
              $max = $grade;
            }


            $completion = new \completion_info($course);

            // First, let's make sure completion is enabled.
            if ($completion->is_enabled()) {
              
              $course->completed = $completion->is_course_complete($user->id);

              $user->course_completed = $course->completed;
             
              if ($course->completed)
                $conclusoes += 1;
            }

            $count_users += 1;
            $useres[$user->id] = $user;
          }
        }
        $category[$key]->arrayusers[$course->fullname] = $useres;
     
      }


      if ($count_users === 0)
        $category[$key]->media = 0;
      else
        $category[$key]->media = round($sum / $count_users, 2);

      $category[$key]->users = $count_users;

      $category[$key]->conclusoes = $conclusoes;

      $category[$key]->courses = $DB->count_records("course", ['category' => intval($categoria->id)]);

      $category[$key]->maxgrade = $max;

    }
    return $category;
  }

  public static function system_fast_report()
  {
    global $DB;

    $response = new stdClass();

    $global_enrollment = 0;

    $global_completion = 0;

    $category = $DB->get_records('course_categories', null, null, "id,name");

    foreach ($category as $key => $categoria) {

      $sum = 0;
      $max = 0;
      $conclusoes = 0;
      $count_users = 0;

      $courses = course_report::getSiteCourses(array('category' => intval($categoria->id)), false);


      foreach ($courses as $course) {
        
        $users = get_enrolled_users(context_course::instance(intval($course->id)), null, null, "u.id,u.firstname, u.lastname");
        foreach ($users as $user) {

          if (intval($user->id) !== 1) {

  

            $completion = new \completion_info($course);

            // First, let's make sure completion is enabled.
            if ($completion->is_enabled()) {
           
              $course->completed = $completion->is_course_complete($user->id);
         
              if ($course->completed)
                $conclusoes += 1;
            }

            $count_users += 1;
          }

        }

      }

      $global_enrollment += $count_users;

      $global_completion += $conclusoes;
    }
    $response->enrollments = $global_enrollment;

    $response->completions = $global_completion;

    set_config('coursesreport_sitecompletion', json_encode($response), 'local_edudashboard');

    return $response;
  }


  public static function admin_fast_report()
  {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/user/lib.php');

    $response = new stdClass();

    // more information label
    $response->more_information = get_string('more_information', 'local_edudashboard');

    // ative and suspended users
    $usercount = $DB->count_records("user", ["suspended" => 0, "deleted" => 0]);
    $response->users = $usercount; //$DB->count_records("user", ["suspended"=>0])-1;//Less guest account;
    $response->suspendedusers = $DB->count_records("user", ["suspended" => 1]);
    $response->active_suspend_users = get_string('active_suspend_users', 'local_edudashboard');

    // courses 
    $response->courses = $DB->count_records("course") - 1; //Less frontpage course;
    $response->courses_label = get_string('courses', 'local_edudashboard');

    // completion courses
      
    $data = json_decode(get_config('local_edudashboard', 'coursesreport_sitecompletion'), false);
    
    $fastreport = $data;
    if ($fastreport->completions) {
      $response->completions = $fastreport->completions;
      $response->completionpercent = round((100 * $fastreport->completions) / $fastreport->enrollments, 1);
      $response->enrollments = $fastreport->enrollments;
    }else{
      $response->completionpercent=0;
    }
    $response->courses_conclusio_label = get_string('courses_conclusion', 'local_edudashboard');

    // today's users
    $params = ['todaysdate' => strtotime("today")];
    $todaysusers = get_users(true, '', false, null, "", '', '', '', '', 
    'id, firstname, lastname, email, lastaccess', 
    "lastaccess >= :todaysdate", $params);
    $response->todaysusers = siteaccess::counttodaysusers(); //sizeof($todaysusers);
    $response->todaysusers_array = $todaysusers;
    $response->authentications_today_label = get_string('authentications_today', 'local_edudashboard');

    return $response;
  }

  public static function getSystemFilesSize()
  {
    global $DB;

    $file_times_mimetype = ['application/zip', 'application/vnd.moodle.backup', 'application/pdf', 'image/jpeg', 'image/png', 'audio/mp3', 'video/mp4'];

    list($insql, $inparams) = $DB->get_in_or_equal($file_times_mimetype, SQL_PARAMS_QM, null);



    $sql = "SELECT sum(filesize) as size FROM {files} WHERE status=0";


    $result = $DB->get_record_sql($sql, $inparams)->size;


    $courses = course_report::getSiteCourses(array(), false);

    $str_crs_size = "";

    foreach ($courses as $course) {

      $result1 = 0;

      $mds = get_course_mods($course->id);

      foreach ($mds as $mod) {
        $sql2 = "SELECT * FROM {context} WHERE instanceid = :instanceid";
        $params = ['instanceid' => $mod->id];
        $records = $DB->get_records_sql($sql2, $params);

        $id = \context_module::instance($mod->id)->id;
        $sql = "SELECT sum(filesize) as course_size FROM {files} WHERE contextid = :contextid AND status = 0";
        $params = ['contextid' => $id];
        $result = $DB->get_record_sql($sql, $params);
        $result1 += round($DB->get_record_sql($sql)->course_size / (1024 * 1024), 2);
      }

      $str_crs_size .= $course->id . "-" . $result1 . ";";
    }

    return array(round($result / (1024 * 1024), 2), $str_crs_size); //In Megabyte;
  }

  public static function isTotara()
  {
    global $CFG;
    return file_exists($CFG->dirroot . "/totara");
  }
  public static function get_program_learners($progid, $status = false)
  {
    global $DB;

    // If status is not false then add a check for it.
    if ($status !== false) {
      $statussql = 'AND status = ?';
      $statusparams = array((int) $status);
    } else {
      $statussql = '';
      $statusparams = array();
    }

    // Query to retrive any users who are registered on the program
    $sql = "SELECT id, firstname, lastname, email FROM {user} WHERE id IN
    (SELECT DISTINCT userid FROM {prog_completion}
    WHERE coursesetid = 0 AND programid = :programid {$statussql})";
    $params = array_merge(['programid' => $progid], $statusparams);
    $users = $DB->get_records_sql($sql, $params);

    $params = array_merge(array($progid), $statusparams);

    return $DB->get_records_sql($sql, $params);
  }

  public static function deteConverter(int $date, bool $showTime = true)
  {

    $data = date('d/m/Y H:i:s', $date);

    $sparar = explode(" ", $data);

    list($day, $month, $ano) = explode("/", $sparar[0]);

    list($hora, $min, $seg) = explode(":", $sparar[1]);

    if ($showTime) {
      return $day . "/" . $month . "/" . $ano . ", às " . $hora . ":" . $min;
    } else {
      return $day . "/" . $month . "/" . $ano;
    }

  }

}