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

use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use local_edudashboard\extra\util;
use local_edudashboard\extra\course_report;
use context_system;
use local_edudashboard\task\site_access_data;

/**
 * Renderable class for exporting EDUDashboard data to templates.
 */
class edudashboard_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $PAGE, $USER, $DB;

        $component = "local_edudashboard";
        $export = new stdClass();
        $context = context_system::instance();
        $export->blocks = [];

        $showadminreport = get_config('local_edudashboard', 'showadminreports');

         site_access_data::categoria_fulldata();
        if (has_capability('mod/data:managetemplates', $context) || $showadminreport == 1) {
            $export->fastreport = util::admin_fast_report();

            if (is_siteadmin()) {
                $export->shou_config = "true";
                $export->configtext = get_string('configs', 'local_edudashboard');
            }
            $export->wwwroot = $CFG->wwwroot;

            $maxdiskocupation = get_config('local_edudashboard', 'maxdiskocupation');

            $maxdocp = "";

            $diskusage = round($this->getsystemfilessize(), 2);

            if ($diskusage < 0) {
                $export->crondisk = 1;
                $export->disk_space = get_string('disk_space', 'local_edudashboard');
                $export->disk_no_data = get_string('disk_no_data', 'local_edudashboard');
            }

            if (floatval($maxdiskocupation) > 0) {
                $maxdocp = " / " . $maxdiskocupation . " GB";

                $export->diskprogress = intval((100 * floatval($diskusage)) / (floatval($maxdiskocupation) * 1024));

            } else {
                $export->diskprogress = 0;
            }

            $PAGE->requires->js_call_amd(
                'local_edudashboard/apexporgresschart',
                'init',
                [$export->diskprogress, "radialchart", [get_string('chart_4_ocupede', 'local_edudashboard')]]
            );

            $diskusage = course_report::datasizeformater($diskusage);

            $export->moodledata_size = " " . $diskusage . $maxdocp;

            $istotara = false;
            if (is_dir($CFG->dirroot . "/totara")) {
                $istotara = true;
            }

            if ($istotara) {
                $PAGE->requires->css(new moodle_url($CFG->wwwroot . "/local/edudashboard/localstyles/totara.styl.cmpt.css"));
            }

            $block = new stdClass();

            $block->content = $PAGE->get_renderer($component)
                ->render(new \local_edudashboard\output\categoriesoverview_renderable());
            $export->blocks[] = $block;

            $block = new stdClass();
            $block->content = $PAGE->get_renderer($component)->render(new \local_edudashboard\output\siteoverview_renderable());
            $export->blocks[] = $block;

            $block = new stdClass();
            $block->content = $PAGE->get_renderer($component)->render(new \local_edudashboard\output\coursessize_renderable());
            $export->courses_size = $block;

        }
        $showhidden = get_config('local_edudashboard', 'show_admin_courses');

        if (is_siteadmin()) {
            if ($showhidden == 1) {
                $export->showadmin = "true";
                $block = new stdClass();
                $block->content = $PAGE->get_renderer($component)
                    ->render(new \local_edudashboard\output\studentcourseoverview_renderable());
                $export->site_user_courses = $block;
            }
        } else {
            $export->showadmin = "true";
            $block = new stdClass();
            $block->content = $PAGE->get_renderer($component)
                ->render(new \local_edudashboard\output\studentcourseoverview_renderable());
            $export->site_user_courses = $block;
        }
        $PAGE->requires->js_call_amd('local_edudashboard/main', 'init');

        return $export;
    }

    /**
     * Returns the total of disk usage
     *
     * @return string
     * @throws \coding_exception
     */
    private function getsystemfilessize() {
        try {
            $cache = \cache::make('local_edudashboard', 'admininfos');
        } catch (\coding_exception $e) {
            return "Erro. Possivelmente a Cahche para 'admininfos' não foi configurado propriamente.";
        }
        $total = $cache->get('totaldiskusage');

        if (!$total) {
            return -1;
        }

        return $total;
    }
}
