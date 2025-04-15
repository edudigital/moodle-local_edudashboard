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
 * Plugin administration pages are defined here.
 *
 * @package     local_edudashboard
 * @category    admin
 * @copyright   2025 edudigital <geral@edudigital-learn.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once('../../config.php');
require_once($CFG->dirroot . '/local/edudashboard/classes/output/renderable.php');

global $CFG, $OUTPUT, $PAGE;

$context = context_system::instance();
$component = "local_edudashboard";

require_login();
require_capability('local/edudashboard:view', $context);

$pageurl = new moodle_url('/local/edudashboard/coursereport.php');
$PAGE->set_context($context);
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('standard');

if (isset($PAGE->theme)) {
    $PAGE->add_body_classes(['theme_' . $PAGE->theme->name]);
}

$PAGE->set_title(get_string('course_report', $component));

$PAGE->navbar->add(get_string("main_name", $component), new moodle_url('/local/edudashboard/index.php'));
$PAGE->navbar->add(get_string('course_report', $component));

$renderable = new \local_edudashboard\output\pagesreport_renderable();

$renderer = $PAGE->get_renderer('local_edudashboard');

echo $OUTPUT->header();
echo $renderer->render_pagesreport($renderable);
echo $OUTPUT->footer();
