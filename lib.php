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

defined('MOODLE_INTERNAL') || die();
/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     local_edudashboard
 * @copyright   2025 edudigital <geral@edudigital-learn.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * Adding "Advanced Dashboard" Menu Link To sidebar
 * @param navigation_node $nav navigation node
 */
function local_edudashboard_extend_navigation_course($navigation, $course)
{
	global $CFG;

	if (has_capability('moodle/course:viewhiddencourses', context_course::instance($course->id))) {
		if ($course->id != 1) {
			// code...

			$icon = new pix_icon('i/stats', '');

			$node = $navigation->add(
				get_string('completionreports', 'local_edudashboard'),
				new moodle_url($CFG->wwwroot . '/local/edudashboard/coursereport.php', array('id' => $course->id)),
				navigation_node::TYPE_CUSTOM,
				'completionreports',
				'completionreports',
				$icon
			);
			$node->showinflatnavigation = true;
		}
	}
}
function local_edudashboard_extend_navigation_user_settings($navigation, $course, $context)
{

}

function local_edudashboard_extend_navigation(global_navigation $navigation)
{
	global $COURSE, $CFG;

	$systemcontext = context_system::instance();

	if (has_capability('local/edudashboard:view', $systemcontext)) {

		$url = new moodle_url('/local/edudashboard/index.php');

		$node = navigation_node::create(get_string('main_name', 'local_edudashboard'), $url, navigation_node::TYPE_CUSTOM, get_string('main_name', 'local_edudashboard'), 'advanceddashboard', new pix_icon('i/report', ''));

		$node->showinflatnavigation = true;
		$navigation->add_node($node);

		//On horizonatal navigation / On top	

		$navigation = $navigation->add(
			get_string('main_name', 'local_edudashboard'),
			$url,
			navigation_node::TYPE_SETTING
		);
		$navigation->showinflatnavigation = false;

	}
	if (has_capability('moodle/course:viewhiddencourses', context_course::instance($COURSE->id))) {
		if ($COURSE->id != 1) {
			// code...

			$icon = new pix_icon('i/stats', '');

			$node = $navigation->add(
				get_string('edudashboard_course_report', 'local_edudashboard'),
				new moodle_url($CFG->wwwroot . '/local/edudashboard/coursereport.php', array('id' => $COURSE->id)),
				navigation_node::TYPE_CUSTOM,
				'completionreports',
				'completionreports',
				$icon
			);
			$node->showinflatnavigation = true;
		}
	}
}
