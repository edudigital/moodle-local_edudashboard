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
 * Plugin version and other meta-data are defined here.
 *
 * @package     local_edudashboard
 * @copyright   2025 edudigital <geral@edudigital-learn.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Adding "Advanced Dashboard" Menu Link To sidebar
 *
 * @param global_navigation $navigation The navigation object to extend
 */
function local_edudashboard_extend_navigation(global_navigation $navigation) {

    $systemcontext = context_system::instance();

    if (has_capability('local/edudashboard:view', $systemcontext)) {
        $url = new moodle_url('/local/edudashboard/index.php');

        $node = navigation_node::create(
            get_string('main_name', 'local_edudashboard'),
            $url,
            navigation_node::TYPE_CUSTOM,
            get_string('main_name', 'local_edudashboard'),
            'advanceddashboard',
            new pix_icon('i/report', '')
        );

        $node->showinflatnavigation = true;
        $navigation->add_node($node);
    }
}
