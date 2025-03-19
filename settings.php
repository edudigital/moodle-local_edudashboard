<?php
// This file is part of Moodle - https://moodle.org/
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
 * Adds admin settings for the plugin.
 *
 * @package     local_edudashboard
 * @category    admin
 * @copyright   2025 edudigital <geral@edudigital-learn.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die();

/* Ensure the configurations for this site are set*/
if ($hassiteconfig) {

     /* Create the new settings page */
     $settings = new admin_settingpage('local_edudashboard', get_string('pluginname', 'local_edudashboard'));

     $ADMIN->add('localplugins', new admin_category('edudashboard', get_string('pluginname', 'local_edudashboard')));
     /* Create settings */
     $settings->add(
         new admin_setting_configcheckbox(
             'local_edudashboard/show_hidden_categories',
             get_string('show_hidden_categories', 'local_edudashboard'),
             "",
             0
         )
     );

     $settings->add(
         new admin_setting_configcheckbox(
             'local_edudashboard/show_admin_courses',
             get_string('show_admin_courses', 'local_edudashboard'),
             "",
             0
         )
     );

     $settings->add(
         new admin_setting_configcheckbox(
             'local_edudashboard/show_admin_reports',
             get_string('show_admin_reports', 'local_edudashboard'),
             "",
             0
         )
     );

     $settings->add(
         new admin_setting_configtext(
             'local_edudashboard/maxdiskocupation',
             get_string('maxdiskocupation', 'local_edudashboard'),
             get_string('maxdiskocupation_help', 'local_edudashboard'),
             '0',
             PARAM_FLOAT
         )
     );

     $ADMIN->add('edudashboard', $settings);

}
