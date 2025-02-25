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

namespace local_edudashboard\external;
use local_edudashboard\task\site_access_data;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_value;
use stdClass;


require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . "/local/edudashboard/classes/constants.php");
require_once($CFG->dirroot . "/local/edudashboard/classes/task/site_access_data.php");

/**
 * Trait implementing the external function local_edudashboard_set_plugin_config.
 */
trait get_siteaccess {

    /**
     * Describes the structure of parameters for the function.
     *
     * @return external_function_parameters
     */
    public static function get_siteaccesss_parameters() {
        return new \external_function_parameters(
            array(
        'startdate' => new \external_value(PARAM_RAW, 'Plugin Name'),
        'enddate' => new \external_value(PARAM_RAW, 'Config Name'),
        'courseid' => new \external_value(PARAM_INT, 'Course id'),

           )
        );
    }

    /**
     * Set plugin configuration
     *
     * @param  string $pluginname Plugin name
     * @param  string $configname Configuration name
     * @return object             COnfiguration
     */
    public static function get_siteaccesss($datestart, $dateend, $courseid=0) {
        // Get Plugin config.

        global $DB;

        $from = 0;

        $to = 0;

        if ($datestart !== "") {

            $from = strtotime($datestart . " 00:00:00");

        }

        if ($dateend !== "") {
            $to = strtotime($dateend . " 23:59:59");
        }

        // Initialize access value for site access.
        $data = array(0, 0, 0, 0, 0, 0, 0);

        $siteaccess = [];

        $filters = [];

        if ($courseid != 0 ) {
            $filters['id'] = $courseid;
        }

        $courses = $DB->get_records("course", $filters, "", 'id,shortname, fullname, timecreated');

        // Getting time strings for access inforamtion block.
        $times = array(
            get_string("time00", "local_edudashboard"),
            get_string("time01", "local_edudashboard"),
            get_string("time02", "local_edudashboard"),
            get_string("time03", "local_edudashboard"),
            get_string("time04", "local_edudashboard"),
            get_string("time05", "local_edudashboard"),
            get_string("time06", "local_edudashboard"),
            get_string("time07", "local_edudashboard"),
            get_string("time08", "local_edudashboard"),
            get_string("time09", "local_edudashboard"),
            get_string("time10", "local_edudashboard"),
            get_string("time11", "local_edudashboard"),
            get_string("time12", "local_edudashboard"),
            get_string("time13", "local_edudashboard"),
            get_string("time14", "local_edudashboard"),
            get_string("time15", "local_edudashboard"),
            get_string("time16", "local_edudashboard"),
            get_string("time17", "local_edudashboard"),
            get_string("time18", "local_edudashboard"),
            get_string("time19", "local_edudashboard"),
            get_string("time20", "local_edudashboard"),
            get_string("time21", "local_edudashboard"),
            get_string("time22", "local_edudashboard"),
            get_string("time23", "local_edudashboard"),
        );

        $times = array_reverse($times, true);
        foreach ($courses as $crc) {

            // code...
            foreach ($times as $time) {
                $siteaccess["crs-" . $crc->id][] = array(
                    "name" => $time,
                    "data" => $data,
                );
            }

            // Initialize access inforamtion object.

        }

        // SQL to get access info log.
        $extra = $courseid != 0 ? " AND courseid = :courseid" : "";
        $sql = "SELECT id, action, courseid, timecreated
            FROM {logstore_standard_log}
            WHERE timecreated >= :timecreated AND courseid > 1 $extra";
        $params = ['timecreated' => $timecreated];

        if ($courseid != 0) {
            $params['courseid'] = $courseid;
        }

        $records = $DB->get_records_sql($sql, $params);

        $sql2 = "SELECT id, action,courseid, timecreated
            FROM {logstore_standard_log}
            WHERE  timecreated >= :timecreated  AND action = :action";

        // Getting access log.

        $params = array(
            "action" => "loggedin",
            "timecreated" => $from /*time() - LOCAL_SITEREPORT_ONEYEAR*/,
        );

        if ($from != 0) {
            $params["timecreated"] = $from;
        } else {
            $params["timecreated"] = $from = strtotime("january");
        }

        if ($to != 0) {
            $params["timecreated2"] = $to;
            $sql .= " AND timecreated <= :timecreated2";

            $sql2 .= " AND timecreated <= :timecreated2";
        }
        if ($courseid != 1) {
            $accesslog = $DB->get_records_sql($sql, $params);

            foreach ($accesslog as $log) {
                // Column for weeks.
                $col = number_format(date("w", $log->timecreated));

                // Row for hours.
                $row = number_format(date("H", $log->timecreated));

                // Calculate site access for row and colums.
                $siteaccess["crs-" . $log->courseid][($row - 23) * -1]['data'][$col]++;

            }
        }

        if ($courseid == 1) {

            $accesslog2 = $DB->get_records_sql($sql2, $params);

            foreach ($accesslog2 as $log2) {
                // Column for weeks.

                $col = number_format(date("w", $log2->timecreated));

                // Row for hours.
                $row = number_format(date("H", $log2->timecreated));

                // Calculate site access for row and colums.
                $siteaccess["crs-1"][($row - 23) * -1]['data'][$col]++;

            }
        }

        return array('login' => \json_encode(site_access_data::site_complete_login($from , $to , $courseid)), 'data' => \json_encode($siteaccess));
    }
    /**
     * Describes the structure of the function return value.
     *
     * @return external_single_structure
     */
    public static function get_siteaccesss_returns() {
        return new \external_single_structure(
            array(
                'login' => new \external_value(PARAM_RAW, 'Site Login', null),
                'data' => new \external_value(PARAM_RAW, 'The Resultd', null),
            )
        );
    }
}