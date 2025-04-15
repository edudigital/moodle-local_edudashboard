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

use local_edudashboard\extra\util;
   /**
    * Get courses size.
    *
    * @param array $courses Course data
    * @return array Chart labels and dataset
    */
class diskusage extends \core\task\scheduled_task {
    /**
     * Returns the name of the disk usage task.
     *
     * @return string The name of the task
     */
    public function get_name() {
        return get_string('disk_usage', 'local_edudashboard');
    }

    /**
     * Execute the task.
     */
    public function execute() {

        $cache = \cache::make('local_edudashboard', 'admininfos');

        list($sitesize, $coursessize) = util::getsystemfilessize();

        $cache->set('totaldiskusage', $sitesize);

        $cache->set('coursesdiskusage', $coursessize);

        return true;
    }
}
