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


class categoriesoverview_renderable implements renderable, templatable
{


    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output)
    {
        global $CFG, $PAGE, $USER;

        $output = new stdClass();

        $candownload = is_siteadmin();

        $data_chart_info = new stdClass();

        $data_chart_info->charttitle = get_string('chart_1_name', 'local_edudashboard');
        // $data_chart_info->chartsubtitle = "Distribuição de notas do curso da platforma";
        $data_chart_info->ytitle =  get_string('chart_1_value', 'local_edudashboard');
        $data_chart_info->chartyAxistitle =  get_string('chart_1_value', 'local_edudashboard');

        $dados = $this->getDataToChart();

        $data_chart_info->xAxis_categories = $dados->names;

        $data_chart_info->series = $dados->series;

        $output->containerid = "edudashboard-overview-container";


        $PAGE->requires->js_call_amd('local_edudashboard/combinationchart', 'init', array($candownload, $data_chart_info, $output->containerid));
        return $output;
    }

    private function getDataToChart($apexchart = false)
    {

        $data = new stdClass();

        $cats_name = [];

        $dtas = [];

        $notas = [];

        $cursos = [];

        $concls = [];

        $users = [];

        $maxgrade = [];

        //$categorias = \local_edudashboard\extra\util::categoria_fulldata();

        $categorias = [];

        $cat_data = get_config('local_edudashboard', 'sitecategoriafulldata');


        if ($cat_data && $cat_data = json_decode($cat_data, false)) {

            $categorias = $cat_data;



        }

        // print_object($courses);

        foreach ($categorias as $categoria) {
            $cats_name[] = $categoria->name;
            $cursos[] = $categoria->courses;
            $notas[] = $categoria->media;
            $users[] = $categoria->users;
            $concls[] = $categoria->conclusoes;
            $maxgrade[] = $categoria->maxgrade;
            //print_object($categoria); 

            //$courses_cats [$course['fullname']] = $course;    
        }
        if ($apexchart) {
            $dtas[] = array('type' => "column", 'name' =>  get_string('chart_1_courses', 'local_edudashboard'), 'data' => $cursos);
            $dtas[] = array('type' => "column", 'name' =>  get_string('chart_1_users', 'local_edudashboard'), 'data' => $users);
            $dtas[] = array('type' => "column", 'name' =>  get_string('chart_1_maxgrade', 'local_edudashboard'), 'data' => $maxgrade);
            $dtas[] = array('type' => "column", 'name' =>  get_string('chart_1_completion', 'local_edudashboard'), 'data' => $concls);
            $dtas[] = array('type' => "line", 'name' =>  get_string('chart_1_avgrade', 'local_edudashboard'), 'data' => $notas);
        } else {
            $dtas[] = array('type' => "column", 'name' => get_string('chart_1_courses', 'local_edudashboard'), 'data' => $cursos);
            $dtas[] = array('type' => "column", 'name' =>  get_string('chart_1_users', 'local_edudashboard'), 'data' => $users);
            $dtas[] = array('type' => "column", 'name' => get_string('chart_1_maxgrade', 'local_edudashboard'), 'data' => $maxgrade);
            $dtas[] = array('type' => "column", 'name' => get_string('chart_1_completion', 'local_edudashboard'), 'data' => $concls);
            $dtas[] = array(
                'type' => "spline",
                'name' => get_string('chart_1_avgrade', 'local_edudashboard'),
                'data' => $notas,
                'marker' => array(

                    'lineWidth' => 1,
                    'lineColor' => '',
                    'fillColor' => 'white'
                )
            );
        }

        $data->names = $cats_name;
        $data->series = $dtas;
        return $data;


    }
}

class studentcourseoverview_renderable implements renderable, templatable
{
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output)
    {
        global $CFG, $PAGE, $USER;

        $output = new stdClass();

        $candownload = is_siteadmin();

        $data_chart_info = new stdClass();

        $data_chart_info->charttitle = get_string('student_report_charttitle','local_edudashboard');
        $data_chart_info->chartsubtitle = get_string('student_report_chartdesc', 'local_edudashboard');
        $data_chart_info->chartyAxistitle = get_string('grade', 'local_edudashboard');

        list($dados, $output->totalcorses, $output->maxgrade, $output->finisheds) = $this->getDataToChart();

        if (sizeof(($dados->names)) > 0) {
            $data_chart_info->xAxis_categories = $dados->names;

            $data_chart_info->series = $dados->series;

            $output->containerid = "edudashboard-course-outcome-container";

            $output->style = "max-width: 100% !important;";

            $PAGE->requires->js_call_amd('local_edudashboard/basiccolumnchart', 'init', array($candownload, $data_chart_info, $output->containerid));
        } else {

            $output->nodata = new stdClass();

            $output->nodata->message = get_string('nocourses', 'local_edudashboard');

        }

        $output->wwwroot = $CFG->wwwroot;

        $output->userid = $USER->id;

        return $output;
    }

    private function getDataToChart()
    {
        global $USER;

        $data = new stdClass();

        $courses_name = [];

        $courses_nota = [];

        $courses_ctn = 0;

        $courses_fnsd = 0;

        $sum_grade = 0;


        $courses = \local_edudashboard\extra\util::mycourses($USER->id);

        foreach ($courses as $course) {
            $courses_name[] = "<a href = '/course/view.php?id=" . $course['id'] . "'>" . $course['fullname'] . "</a>"; //$course['fullname'];
            $courses_nota[] = $grade = doubleval($course['rawgrade']);
            if ($course['finished']) {
                $courses_fnsd++;
            }
            $courses_ctn++;

            $grade = $course['maxgrade'] != 0 ? $grade * 100 / $course['maxgrade'] : 0; //Nota de 0 - 100
            $sum_grade += $grade;

            //$courses_cats [$course['fullname']] = $course;    
        }

        $data->names = $courses_name;

        $data->series = [array('name' => get_string('grade', 'local_edudashboard'), 'data' => $courses_nota)];

        return array($data, $courses_ctn, $courses_ctn == 0 ? 0 : round($sum_grade / $courses_ctn, 1), $courses_fnsd);


    }

}

class siteoverview_renderable implements renderable, templatable
{
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output)
    {
        global $CFG, $PAGE, $USER;

        $output = new stdClass();

        $candownload = is_siteadmin();

        $output->wwwroot = $CFG->wwwroot;

        $data_chart_info = new stdClass();

        $data_chart_info->charttitle = get_string('chart_2_name', 'local_edudashboard');
        $data_chart_info->chartsubtitle = get_string('chart_2_name2', 'local_edudashboard');
        $data_chart_info->chartyAxistitle = get_string('chart_1_value', 'local_edudashboard');;

        $dados = $this->getDataToChart();

        $data_chart_info->series = $dados->series;

        $data_chart_info->drilldown = $dados->drilldown;

        $output->containerid = "edudashboard-sitecourse-overview-container";

        $PAGE->requires->js_call_amd('local_edudashboard/drilldownchart', 'init', array($candownload, $data_chart_info, $output->containerid));
        return $output;
    }

    private function getDataToChart()
    {

        $data = new stdClass();

        $serie = new stdClass();

        $drilldown = [];

        $serie->name = get_string('courses', 'local_edudashboard');

        $serie->colorByPoint = true;

        $categorias = [];

        $cat_data = get_config('local_edudashboard', 'sitecategoriafulldata');
        if ($cat_data && $cat_data = json_decode($cat_data, false)) {

            $categorias = $cat_data;

        }

        //$categorias = \local_edudashboard\extra\util::categoria_fulldata();



        foreach ($categorias as $categoria) {

            //print_object($categoria);

            $serie->data[] = array('name' => $categoria->name, 'subject' => get_string('courses1', 'local_edudashboard'), 'y' => $categoria->courses, 'drilldown' => $categoria->courses > 0 ? $categoria->name : 0);
            if (isset($categoria->arrayusers)) {
                $drillserie = new stdClass();
                $drillserie->name = get_string('users', 'local_edudashboard');
                $drillserie->id = $categoria->name;
                $drillserie->subject = get_string('users1', 'local_edudashboard');
                foreach ($categoria->arrayusers as $name => $users) {
                    $users_cnt = count((array) $users);
                    $drillserie->data[] = array('name' => $name, 'subject' => get_string('users1', 'local_edudashboard'), 'y' => $users_cnt, 'drilldown' => $users_cnt > 0 ? $name : '');
                    $drillserie2 = new stdClass();
                    $drillserie2->name = get_string('grade', 'local_edudashboard');
                    $drillserie2->id = $name;
                    $drillserie2->subject = get_string('users1', 'local_edudashboard');
                    if ($users_cnt <= 100) {
                        foreach ($users as $index => $usr) {
                            // print_object($usr);
                            $drillserie2->data[] = array('name' => $usr->firstname . " " . $usr->lastname, 'subject' => "pts.", 'y' => $usr->grade); //Replace $index to grade                              
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
class sitecompletion_renderable implements renderable, templatable
{
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */

    public function export_for_template(renderer_base $output)
    {
        global $CFG, $OUTPUT, $PAGE;
        require_once($CFG->dirroot . '/local/edudashboard/classes/form/selectcourse_form.php');

        $sort = optional_param('sort', '', PARAM_TEXT);
        $learningobject = optional_param('lb', -1, PARAM_INT);

        $output = new stdClass();

        if (util::isTotara() & $learningobject != -1) {
            if ($learningobject == 0) {
                list($output->courses, $output->total_enrollemnts, $output->global_completion_percentage, $output->total_completed, $output->global_size) = course_report::getSitecoursesCompletion();
            } elseif ($learningobject == 1) {
               // list($output->courses, $output->total_enrollemnts, $output->global_completion_percentage, $output->total_completed, $output->global_size) = course_report::getSiteProgramsCompletion();
            }
           
            $mform = new courseorprogram_form();
            $output->selectcourse_form = $mform->render();
            $PAGE->requires->js_call_amd('local_edudashboard/learnobjectselector', 'laodMyForm', [$CFG->wwwroot]);

        } else {
            list($output->courses, $output->total_enrollemnts, $output->global_completion_percentage, $output->total_completed, $output->global_size) = course_report::getSitecoursesCompletion();
        }

          
        if ($sort !== '') {
            usort($output->courses, function ($a, $b) {
                $sort = optional_param('sort', '', PARAM_TEXT);
                $dir = optional_param('dir', 'ASC', PARAM_TEXT);
                if ($dir === "DESC") {
                    return intval(((array) $b)[$sort]) < intval(((array) $a)[$sort]);
                } else
                    return intval(((array) $b)[$sort]) > intval(((array) $a)[$sort]);

            }); // course_report::array_sort($output->courses,$sort,SORT_ASC);
        }

        // strigns
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
      
        $PAGE->requires->js_call_amd('local_edudashboard/chartjsbar', 'init', $this->getDataToChart($output->courses));

        $output->export =  $OUTPUT->download_dataformat_selector(get_string('exportto', 'local_edudashboard'), 'exportdatas.php', 'dataformat', array('reporttype'=>1, 'filter'=>''));


        return $output;
    }

    private function getDataToChart($courses)
    {


        $labels = [];
        $data_inscrito = [];
        $data_concluido = [];
        $data_p_concluido = [];

        foreach ($courses as $course) {
            $labels[] = $course->fullname;
            $data_inscrito[] = ($course->total_enrolled > 0) ? $course->total_enrolled : 0;
            $data_concluido[] = ($course->completedusers > 0) ? $course->completedusers : 0;
            $data_p_concluido[] = ($course->completedusers_percentage > 0) ? $course->completedusers_percentage : 0;

        }

        

        $dataset = [
            array(
                "label" => get_string('users_courses_report', 'local_edudashboard'),
                "backgroundColor" => "rgb(136 189 36 / 62%)",
                "borderColor" => "rgb(136 189 36 / 100%)",
                "borderWidth" => 2,
                "hoverBackgroundColor" => ("Utils.transparentize(Utils.CHART_COLORS.red, 0.5)"),
                "hoverBorderColor" => "#47a6ff",
                "data" => $data_inscrito,
            ),
            array(
                "label" => get_string('users_courses_report_conclude', 'local_edudashboard'),
                "backgroundColor" => "#0083ff5c",
                "borderColor" => "#47a6ff",
                "borderWidth" => 2,
                "hoverBackgroundColor" => "#0083ff5c",
                "hoverBorderColor" => "#47a6ff",
                "data" => $data_concluido,
            ),
           /* array(
                "type" => "line",
                "label" => get_string('conclusion_percentage_label', 'local_edudashboard'),
                "backgroundColor" => "#f5365c",
                "borderColor" => "#f5365c",
                "borderWidth" => 2,
                "hoverBackgroundColor" => ("Utils.transparentize(Utils.CHART_COLORS.red, 0.5)"),
                "hoverBorderColor" => "#47a6ff",
                "data" => $data_p_concluido,
            ),*/

        ];

        return array($labels, $dataset);
    }
}

class coursessize_renderable implements renderable, templatable
{

    public function export_for_template(renderer_base $output)
    {
        global $CFG, $PAGE, $USER;

        $output = new stdClass();
        $component = "local_edudashboard";

        $userid = optional_param('id', 0, PARAM_INT);

        $candownload = is_siteadmin();

        $data_chart_info = new stdClass();

        $data_chart_info->charttitle = get_string('chart_3_name', 'local_edudashboard');;
        //$data_chart_info->chartsubtitle = "Distribuição de notas por curso";
        $data_chart_info->chartyleftAxistitle = get_string('chart_3_size', 'local_edudashboard');;

        $data_chart_info->chartyrighttAxistitle = get_string('chart_3_activities', 'local_edudashboard');;

        $dados = $this->getDataToChart($userid, true);

        $data_chart_info->xAxis_categories = $dados->names;

        $data_chart_info->series = $dados->series;

        $output->containerid = "edudashboard-coursessize-container";

        $output->style = "max-width: 100% !important;";

        $PAGE->requires->js_call_amd('local_edudashboard/apexcombinationchart', 'init', array($candownload, $data_chart_info, $output->containerid));

        return $output;
    }



    private function getDataToChart($uid, $apexchart = false)
    {

        $data = new stdClass();

        $cats_name = [];


        $dtas = [];

        $sizes = [];

        $mods = [];

        $courses = course_report::getSiteCourses(array(), false);


        $courses_size = course_report::getCoursesFilesSize();

        // print_object($courses);

        foreach ($courses as $course) {
            $cats_name[] = $course->fullname;
            $sizes[] = doubleval($courses_size[$course->id]);
            $mods[] = sizeof(get_course_mods($course->id));
        }

        if ($apexchart) {
            $dtas[] = array('type' => "column", 'name' => get_string('chart_3_label1', 'local_edudashboard'), 'data' => $sizes);
            $dtas[] = array('type' => "line", 'name' => get_string('chart_3_activities', 'local_edudashboard'), 'data' => $mods);
        } else {
            $dtas[] = array(
                'type' => "bar",
                'name' => get_string('chart_3_label1', 'local_edudashboard'),
                'data' => $sizes,
                'marker' => array(

                    'lineWidth' => 1,
                    'lineColor' => '',
                    'fillColor' => 'white'
                )
            );
        }


        $data->names = $cats_name;
        $data->series = $dtas;
        return $data;

    }

}

