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

namespace local_edudashboard\task;

use local_edudashboard\extra\course_report;
use local_edudashboard\extra\util;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . "/local/edudashboard/classes/constants.php");
require_once($CFG->libdir . "/completionlib.php");
require_once($CFG->dirroot . '/grade/querylib.php');
require_once($CFG->libdir . '/gradelib.php');

use cache;
use context_course;


/**
 * Scheduled Task to Update Report Plugin Table.
 */
class site_access_data extends \core\task\scheduled_task {

    /**
     * Can run cron task.
     *
     * @return boolean
     */
    public function can_run(): bool {
        return true;
    }

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return "EDUDashboard Site Access Task";
    }

    /**
     * Executes the site access data collection task.
     *
     * @return bool True on successful execution
     */
    public function execute() {
        mtrace("--->>Lets take site acess data");

        $categoriafulldata = $this->categoria_fulldata();

        util::system_fast_report();

        set_config('sitecategoriafulldata', json_encode($categoriafulldata), 'local_edudashboard');

        unset_config('siteaccessrecalculate', 'local_edudashboard');

        cache::make('local_edudashboard', 'siteaccess')->purge();

        return true;
    }

    /**
     * Retrieves full data for course categories, including user grades and completion stats.
     *
     * @return array An array of category objects with detailed user and course data
     */
    public static function categoria_fulldata() {
        global $DB;

        $showhiddencategories = get_config('local_edudashboard', 'show_hidden_categories');
        $conditions = $showhiddencategories == 0 ? ['visible' => 1, 'visibleold' => 1] : null;
        $category = $DB->get_records('course_categories', $conditions, 'name ASC', 'id, visible, name');

        foreach ($category as $key => $categoria) {
            $sum = 0.0; // Garantir que $sum seja float.
            $max = 0.0; // Garantir que $max seja float.
            $conclusoes = 0;
            $categoriafulldata = 0;

            $courses = course_report::getsitecourses(['category' => intval($categoria->id)], false);

            foreach ($courses as $course) {
                $useres = [];
                $users = get_enrolled_users(
                    context_course::instance(intval($course->id)),
                    null,
                    null,
                    'u.id, u.firstname, u.lastname',
                    'u.firstname ASC'
                );

                foreach ($users as $user) {
                    if (intval($user->id) !== 1) { // Exclui usuário guest/admin.
                        $usergrade = \grade_get_course_grade($user->id, $course->id);
                        $grade = $usergrade->grade !== null ? round($usergrade->grade, 2) : 0.0; // Trata null como 0.

                        $sum += $grade;

                        $user->grade = $grade;

                        if ($grade >= $max) {
                            $max = $grade;
                        }

                        $completion = new \completion_info($course);
                        if ($completion->is_enabled()) {
                            $course->completed = $completion->is_course_complete($user->id);
                            $user->coursecompleted = $course->completed;

                            if ($course->completed) {
                                $conclusoes += 1;
                            }
                        }
                        $categoriafulldata += 1;
                        $useres[$user->id] = $user;
                    }
                }
                $category[$key]->arrayusers[$course->fullname] = $useres;
            }

            // Calcula a média apenas se houver usuários ($categoriafulldata > 0).
            if ($categoriafulldata > 0 && $sum !== null) {
                $category[$key]->media = round($sum / $categoriafulldata, 2);
            } else {
                $category[$key]->media = 0.0;
            }

            $category[$key]->users = $categoriafulldata;
            $category[$key]->conclusoes = $conclusoes;
            $category[$key]->courses = $DB->count_records('course', ['category' => intval($categoria->id)]);
            $category[$key]->maxgrade = $max;

            util::admin_fast_report();
        }

        return $category;
    }
}
