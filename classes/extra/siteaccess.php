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


global $CFG;

/**
 * class siteaccess
 */
class siteaccess {
    /**
     * function counttodaysusers
     */
    public static function counttodaysusers() {
        global $DB;

        return $DB->get_record_sql("SELECT count(id) as logins
                                  FROM {logstore_standard_log}
                                  WHERE action ='loggedin' and timecreated >= ".strtotime("today"))->logins;
    }


}
