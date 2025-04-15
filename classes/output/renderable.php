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

namespace local_edudashboard\output;

defined('MOODLE_INTERNAL') || die();
use renderable;
use renderer_base;
use stdClass;
use templatable;
use local_edudashboard\extra\course_report;
use local_edudashboard\extra\util;


require_once("$CFG->libdir/tablelib.php");
require_once("$CFG->libdir/blocklib.php");

/**
 * Renderable class for categories overview.
 */
class categoriesoverview_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $PAGE, $USER;

        $output = new stdClass();

        $candownload = is_siteadmin();

        $datachartinfo = new stdClass();

        $datachartinfo->charttitle = get_string('chart_1_name', 'local_edudashboard');
        $datachartinfo->ytitle = get_string('chart_1_value', 'local_edudashboard');
        $datachartinfo->chartyAxistitle = get_string('chart_1_value', 'local_edudashboard');

        $dados = $this->getdatatochart();

        $datachartinfo->xAxis_categories = $dados->names;

        $datachartinfo->series = $dados->series;

        $output->containerid = "edudashboard-overview-container";
        $PAGE->requires->js_call_amd('local_edudashboard/combinationchart', 'init',
        [$candownload, $datachartinfo, $output->containerid]);
        return $output;
    }

    /**
     * Get data for chart rendering.
     *
     * @param bool $apexchart Whether to use Apex chart format
     * @return stdClass Chart data
     */
    private function getdatatochart($apexchart = false) {

        $data = new stdClass();

        $catsname = [];

        $dtas = [];

        $notas = [];

        $cursos = [];

        $concls = [];

        $users = [];

        $maxgrade = [];

        $categorias = [];

        $catdata = get_config('local_edudashboard', 'sitecategoriafulldata');

        if ($catdata && $catdata = json_decode($catdata , false)) {

            $categorias = $catdata;
        }
        foreach ($categorias as $categoria) {
            $catsname[] = $categoria->name;
            $cursos[] = $categoria->courses;
            $notas[] = $categoria->media;
            $users[] = $categoria->users;
            $concls[] = $categoria->conclusoes;
            $maxgrade[] = $categoria->maxgrade;
        }
        if ($apexchart) {
            $dtas[] = ['type' => 'column', 'name' => get_string('chart_1_courses', 'local_edudashboard'), 'data' => $cursos];
            $dtas[] = ['type' => 'column', 'name' => get_string('chart_1_users', 'local_edudashboard'), 'data' => $users];
            $dtas[] = ['type' => 'column', 'name' => get_string('chart_1_maxgrade', 'local_edudashboard'), 'data' => $maxgrade];
            $dtas[] = ['type' => 'column', 'name' => get_string('chart_1_completion', 'local_edudashboard'), 'data' => $concls];
            $dtas[] = ['type' => 'line', 'name' => get_string('chart_1_avgrade', 'local_edudashboard'), 'data' => $notas];
        } else {
            $dtas[] = ['type' => 'column', 'name' => get_string('chart_1_courses', 'local_edudashboard'), 'data' => $cursos];
            $dtas[] = ['type' => 'column', 'name' => get_string('chart_1_users', 'local_edudashboard'), 'data' => $users];
            $dtas[] = ['type' => 'column', 'name' => get_string('chart_1_maxgrade', 'local_edudashboard'), 'data' => $maxgrade];
            $dtas[] = ['type' => 'column', 'name' => get_string('chart_1_completion', 'local_edudashboard'), 'data' => $concls];
            $dtas[] = [
                'type' => 'spline',
                'name' => get_string('chart_1_avgrade', 'local_edudashboard'),
                'data' => $notas,
                'marker' => [
                    'lineWidth' => 1,
                    'lineColor' => '',
                    'fillColor' => 'white',
                ],
            ];
        }

        $data->names = $catsname;
        $data->series = $dtas;
        return $data;
    }
}

/**
 * Renderable class for student course overview.
 */
class studentcourseoverview_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $PAGE, $USER;

        $output = new stdClass();

        $candownload = is_siteadmin();

        $datachartinfo = new stdClass();

        $datachartinfo->charttitle = get_string('student_report_charttitle', 'local_edudashboard');
        $datachartinfo->chartsubtitle = get_string('student_report_chartdesc', 'local_edudashboard');
        $datachartinfo->chartyAxistitle = get_string('grade', 'local_edudashboard');

        list($dados, $output->totalcorses, $output->maxgrade, $output->finisheds) = $this->getdatatochart();

        if (count(($dados->names)) > 0) {
            $datachartinfo->xAxis_categories = $dados->names;

            $datachartinfo->series = $dados->series;

            $output->containerid = "edudashboard-course-outcome-container";

            $output->style = "max-width: 100% !important;";

            $PAGE->requires->js_call_amd('local_edudashboard/basiccolumnchart', 'init',
            [$candownload, $datachartinfo, $output->containerid]);
        } else {

            $output->nodata = new stdClass();

            $output->nodata->message = get_string('nocourses', 'local_edudashboard');

        }

        $output->wwwroot = $CFG->wwwroot;

        $output->userid = $USER->id;

        return $output;
    }

    /**
     * Get data for chart rendering.
     *
     * @return array Chart data and statistics
     */
    private function getdatatochart() {
        global $USER;

        $data = new stdClass();

        $coursesname = [];

        $coursesnota = [];

        $coursesctn = 0;

        $coursesfnsd = 0;

        $sumgrade = 0;
        $courses = \local_edudashboard\extra\util::mycourses($USER->id);

        foreach ($courses as $course) {
            $coursesname[] = "<a href='/course/view.php?id={$course['id']}'>{$course['fullname']}</a>";
            $grade = doubleval($course['rawgrade']);
            $coursesnota[] = $grade;
            if ($course['finished']) {
                $coursesfnsd++;
            }
            $coursescnt++;
            $grade = $course['maxgrade'] != 0 ? $grade * 100 / $course['maxgrade'] : 0; // Nota de 0 - 100.
            $sumgrade += $grade;
        }

        $data->names = $coursesname;
        $data->series = [['name' => get_string('grade', 'local_edudashboard'), 'data' => $coursesnota]];

        return [$data, $coursescnt, $coursescnt == 0 ? 0 : round($sumgrade / $coursescnt, 1), $coursesfnsd];
    }

}
/**
 * Renderable class for site overview.
 */
class siteoverview_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {

        global $CFG, $PAGE, $USER;

        $output = new stdClass();

        $candownload = is_siteadmin();

        $output->wwwroot = $CFG->wwwroot;

        $datachartinfo = new stdClass();

        $datachartinfo->charttitle = get_string('chart_2_name', 'local_edudashboard');
        $datachartinfo->chartsubtitle = get_string('chart_2_name2', 'local_edudashboard');
        $datachartinfo->chartyAxistitle = get_string('chart_1_value', 'local_edudashboard');;

        $dados = $this->getdatatochart();

        $datachartinfo->series = $dados->series;

        $datachartinfo->drilldown = $dados->drilldown;

        $output->containerid = "edudashboard-sitecourse-overview-container";

        $PAGE->requires->js_call_amd('local_edudashboard/drilldownchart', 'init',
        [ $candownload, $datachartinfo, $output->containerid ]);
        return $output;
    }
    /**
     * Get data for chart rendering.
     *
     * @return array Chart data and statistics
     */
    private function getdatatochart() {

        $data = new stdClass();

        $serie = new stdClass();

        $drilldown = [];

        $serie->name = get_string('courses', 'local_edudashboard');

        $serie->colorByPoint = true;

        $categorias = [];

        $catdata = get_config('local_edudashboard', 'sitecategoriafulldata');
        if ($catdata && $catdata = json_decode($catdata , false)) {

            $categorias = $catdata;

        }

        foreach ($categorias as $categoria) {

            $serie->data[] = ['name' => $categoria->name, 'subject' => get_string('courses1', 'local_edudashboard'), 'y'
            => $categoria->courses, 'drilldown' => $categoria->courses > 0 ? $categoria->name : 0];
            if (isset($categoria->arrayusers)) {
                $drillserie = new stdClass();
                $drillserie->name = get_string('users', 'local_edudashboard');
                $drillserie->id = $categoria->name;
                $drillserie->subject = get_string('users1', 'local_edudashboard');
                foreach ($categoria->arrayusers as $name => $users) {
                    $userscnt = count((array) $users);
                    $drillserie->data[] = ['name' => $name, 'subject' => get_string('users1', 'local_edudashboard'),
                    'y' => $userscnt, 'drilldown' => $userscnt > 0 ? $name : ''];
                    $drillserie2 = new stdClass();
                    $drillserie2->name = get_string('grade', 'local_edudashboard');
                    $drillserie2->id = $name;
                    $drillserie2->subject = get_string('users1', 'local_edudashboard');
                    if ($userscnt <= 100) {
                        foreach ($users as $index => $usr) {

                            $drillserie2->data[] = ['name' => $usr->firstname . " " . $usr->lastname,
                            'subject' => "pts.", 'y' => $usr->grade];
                        }
                        $drilldown[] = $drillserie2;
                    }
                    $drilldown[] = $drillserie;
                }
            }

        }

        $data->series[] = $serie;
        $data->drilldown = $drilldown;
        return $data;
    }
}
/**
 * Renderable class for site completion.
 */
class sitecompletion_renderable implements renderable, templatable {

    /**
     * Export data for template rendering.
     *
     * @param renderer_base $output Renderer instance
     * @return stdClass Data for template
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $OUTPUT, $PAGE;
        require_once($CFG->dirroot . '/local/edudashboard/classes/form/selectcourse_form.php');
        require_once($CFG->dirroot . '/local/edudashboard/forms/siteloginfilter_form.php');
        require_once($CFG->dirroot . '/local/edudashboard/forms/siteaccess_form.php');
        require_once($CFG->dirroot . '/local/edudashboard/forms/courseorprogram_form.php');

        $sort = optional_param('sort', '', PARAM_TEXT);
        $learningobject = optional_param('lb', -1, PARAM_INT);

        $output = new stdClass();

        if (util::istotara() & $learningobject != -1) {
            if ($learningobject == 0) {
                list($output->courses, $output->total_enrollemnts, $output->global_completion_percentage,
                $output->total_completed, $output->global_size) = course_report::getsitecoursescompletion();
            }

            $mform = new courseorprogram_form();
            $output->selectcourse_form = $mform->render();
            $PAGE->requires->js_call_amd('local_edudashboard/learnobjectselector', 'laodMyForm', [$CFG->wwwroot]);

        } else {
            list($output->courses, $output->total_enrollemnts, $output->global_completion_percentage,
            $output->total_completed, $output->global_size) = course_report::getsitecoursescompletion();
        }
        if ($sort !== '') {
            usort($output->courses, function ($a, $b) {
                $sort = optional_param('sort', '', PARAM_TEXT);
                $dir = optional_param('dir', 'ASC', PARAM_TEXT);
                if ($dir === "DESC") {
                    return intval(((array) $b)[$sort]) < intval(((array) $a)[$sort]);
                }
                    return intval(((array) $b)[$sort]) > intval(((array) $a)[$sort]);
            });
        }
        $output->completion_report_label = get_string('completion_report', 'local_edudashboard');
        $output->course_label = get_string('course_label', 'local_edudashboard');
        $output->total_users_label = get_string('total_users_label', 'local_edudashboard');
        $output->course_completion_label = get_string('course_completion_label', 'local_edudashboard');
        $output->course_completion_label1 = get_string('course_completion_label1', 'local_edudashboard');
        $output->conclusion_percentage_label = get_string('conclusion_percentage_label', 'local_edudashboard');
        $output->disk_size_label = get_string('disk_size_label', 'local_edudashboard');
        $output->without_data = get_string('without_data', 'local_edudashboard');
        $output->total_avg = get_string('total_avg', 'local_edudashboard');
        $output->wwwroot = $CFG->wwwroot;
        $PAGE->requires->js_call_amd('local_edudashboard/chartjsbar', 'init', $this->getdatatochart($output->courses));
        $output->export = $OUTPUT->download_dataformat_selector(get_string('exportto', 'local_edudashboard'),
        'exportdatas.php', 'dataformat', ['reporttype' => 1, 'filter' => '']);
        return $output;
    }
    /**
     * Get data for chart rendering.
     *
     * @param array $courses Course data
     * @return array Chart labels and dataset
     */
    private function getdatatochart($courses) {
        $labels = [];
        $datainscrito = [];
        $dataconcluido = [];
        foreach ($courses as $course) {
            $labels[] = $course->fullname;
            $datainscrito[] = ($course->total_enrolled > 0) ? $course->total_enrolled : 0;
            $dataconcluido[] = ($course->completedusers > 0) ? $course->completedusers : 0;
        }

        $dataset = [
           [
                "label" => get_string('users_courses_report', 'local_edudashboard'),
                "backgroundColor" => "rgb(136 189 36 / 62%)",
                "borderColor" => "rgb(136 189 36 / 100%)",
                "borderWidth" => 2,
                "hoverBackgroundColor" => ("Utils.transparentize(Utils.CHART_COLORS.red, 0.5)"),
                "hoverBorderColor" => "#47a6ff",
                "data" => $datainscrito,
            ],
           [
                "label" => get_string('users_courses_report_conclude', 'local_edudashboard'),
                "backgroundColor" => "#0083ff5c",
                "borderColor" => "#47a6ff",
                "borderWidth" => 2,
                "hoverBackgroundColor" => "#0083ff5c",
                "hoverBorderColor" => "#47a6ff",
                "data" => $dataconcluido,
           ],

        ];

        return [$labels, $dataset];
    }
}
    /**
     * Get courses size.
     *
     */
class coursessize_renderable implements renderable, templatable {


    /**
     * Get courses size.
     *
     * @param renderer_base $output Course data
     * @return stdClass Chart labels and dataset
     */
    public function export_for_template(renderer_base $output) {

        global  $PAGE;

        $output = new stdClass();

        $userid = optional_param('id', 0, PARAM_INT);

        $candownload = is_siteadmin();

        $datachartinfo = new stdClass();

        $datachartinfo->charttitle = get_string('chart_3_name', 'local_edudashboard');;

        $datachartinfo->chartyleftAxistitle = get_string('chart_3_size', 'local_edudashboard');;

        $datachartinfo->chartyrighttAxistitle = get_string('chart_3_activities', 'local_edudashboard');;

        $dados = $this->getdatatochart($userid, true);

        $datachartinfo->xAxis_categories = $dados->names;

        $datachartinfo->series = $dados->series;

        $output->containerid = "edudashboard-coursessize-container";

        $output->style = "max-width: 100% !important;";

        $PAGE->requires->js_call_amd('local_edudashboard/apexcombinationchart', 'init',
        [$candownload, $datachartinfo, $output->containerid]);

        return $output;
    }


    /**
     * Get data to chart rendering.
     *
     * @param int $uid The user ID for filtering data
     * @param bool $apexchart Whether to use ApexCharts format (optional, default false)
     * @return stdClass Chart labels and dataset
     */
    private function getdatatochart($uid, $apexchart = false) {

        $data = new stdClass();
        $catsname = [];
        $dtas = [];
        $sizes = [];
        $mods = [];

        $courses = course_report::getsitecourses([], false);
        $coursessize = course_report::getcoursefilessize();

        foreach ($courses as $course) {
            $catsname[] = $course->fullname;

            /* Verifica se o tamanho do curso existe em $coursessize, caso contrário, usa 0*/
            $size = isset($coursessize[$course->id]) ? doubleval($coursessize[$course->id]) : 0;
            $sizes[] = $size;

            $mods[] = count(get_course_mods($course->id));
        }

        if ($apexchart) {
            $dtas[] = ['type' => "column", 'name' => get_string('chart_3_label1', 'local_edudashboard'), 'data' => $sizes];
            $dtas[] = ['type' => "line", 'name' => get_string('chart_3_activities', 'local_edudashboard'), 'data' => $mods];
        } else {
            $dtas[] = [
                'type' => "bar",
                'name' => get_string('chart_3_label1', 'local_edudashboard'),
                'data' => $sizes,
                'marker' => [
                    'lineWidth' => 1,
                    'lineColor' => '',
                    'fillColor' => 'white',
                ],
                ];
        }

        $data->names = $catsname;
        $data->series = $dtas;
        return $data;
    }

}



