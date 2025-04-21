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
 * Renderable class for pages report.
 *
 * @package     local_edudashboard
 * @category    admin
 * @copyright   2025 edudigital <geral@edudigital-learn.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edudashboard\output;
use renderable;
use templatable;
use renderer_base;
use stdClass;

/**
 * Renderable class for pages report.
 */
class pagesreport_renderable implements renderable, templatable {
    /**
     * @var string The type of report to render
     */
    public $reportype;

    /**
     * Constructs the pages report renderable object.
     *
     * @param string $reportype The type of report to render (default: 'course')
     */
    public function __construct($reportype = 'course') {
        $this->reportype = $reportype;
    }

    /**
     * Exports data for template rendering.
     *
     * @param renderer_base $output The renderer object used for template rendering
     * @return stdClass Data object for the template
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->premium_url = 'https://edudigital-learn.com/';

        if ($this->reportype === 'authentication') {
            $data->report_title = get_string('authentication_report_title', 'local_edudashboard');
            $data->report_description = get_string('authentication_report_description', 'local_edudashboard');
            $data->report_features = get_string('authentication_report_features', 'local_edudashboard');

            $data->features_list = [
                get_string('authentication_report_feature_users', 'local_edudashboard'),
                get_string('authentication_report_feature_filter', 'local_edudashboard'),
                get_string('authentication_report_feature_dates', 'local_edudashboard'),
                get_string('authentication_report_feature_charts', 'local_edudashboard'),
                get_string('authentication_report_feature_trends', 'local_edudashboard'),
            ];

            $data->premium_notification = get_string('premium_notification', 'local_edudashboard');
            $data->upgrade_premium = get_string('upgrade_premium', 'local_edudashboard');

            $data->report_content = get_string('authentication_report_description', 'local_edudashboard');
        } else {
            $data->report_title = get_string('course_report_title', 'local_edudashboard');
            $data->report_description = get_string('course_report_description', 'local_edudashboard');
            $data->report_features = get_string('course_report_features', 'local_edudashboard');

            $data->features_list = [
                get_string('course_report_feature_enrolled', 'local_edudashboard'),
                get_string('course_report_feature_completed', 'local_edudashboard'),
                get_string('course_report_feature_completion_rate', 'local_edudashboard'),
                get_string('course_report_feature_data_size', 'local_edudashboard'),
            ];

            $data->premium_notification = get_string('premium_notification', 'local_edudashboard');
            $data->upgrade_premium = get_string('upgrade_premium', 'local_edudashboard');
        }

        return $data;
    }
}
