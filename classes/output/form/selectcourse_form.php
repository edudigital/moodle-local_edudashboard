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

use local_edudashboard\extra\course_report;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once("$CFG->libdir/formslib.php");

/**
 * Form for selecting a course.
 */
class selectcourse_form extends \moodleform {
    /**
     * Define the form elements.
     */
    public function definition() {
        global $CFG;

        $courseid = optional_param('id', 0, PARAM_INT);
        $mform = $this->_form;

        $courses = course_report::getsitecourses([], false);
        $options = [];

        foreach ($courses as $course) {
            if ($course->id != 1) { // Exclude front page.
                $options[$course->id] = $course->fullname . " ({$course->shortname})";
            }
        }

        // Add select element for courses.
        $select = $mform->addElement('select', 'courses', get_string('selectedcourse', 'local_edudashboard'), $options, [
            'id' => 'course_select',
        ]);
        $select->setSelected(intval($courseid));

        $mform->disable_form_change_checker();
    }

    /**
     * Custom validation for the form.
     *
     * @param array $data Form data
     * @param array $files Form files
     * @return array Validation errors
     */
    public function validation($data, $files) {
        return [];
    }
}

/**
 * Form for site access with course and date filters.
 */
class siteaccess_form extends \moodleform {
    /**
     * Define the form elements.
     */
    public function definition() {
        global $CFG;

        $courseid = optional_param('id', 1, PARAM_INT);
        $mform = $this->_form;

        $courses = course_report::getsitecourses([], false);
        $options = [];

        foreach ($courses as $course) {
            if ($course->id == 1) {
                $options[$course->id] = $course->fullname . ' (Site)';
            } else {
                $options[$course->id] = $course->fullname . " ({$course->shortname})";
            }
        }

        // Add select element for courses.
        $select = $mform->addElement('select', 'courses', get_string('selectedcourse', 'local_edudashboard'), $options, [
            'id' => 'course_select',
        ]);
        $select->setSelected(intval($courseid));

        // Add date selectors.
        $mform->addElement('date_selector', 'fromdate', get_string('fromdate', 'local_edudashboard'), [
            'optional' => true,
        ], [
            'id' => 'timefrom',
        ]);

        $mform->addElement('date_selector', 'todate', get_string('todate', 'local_edudashboard'), [
            'stopyear' => intval(date('Y')),
            'optional' => true,
        ], [
            'id' => 'timeto',
        ]);

        $mform->disable_form_change_checker();
    }

    /**
     * Custom validation for the form.
     *
     * @param array $data Form data
     * @param array $files Form files
     * @return array Validation errors
     */
    public function validation($data, $files) {
        return [];
    }
}

/**
 * Form for selecting course or program.
 */
class courseorprogram_form extends \moodleform {
    /**
     * Define the form elements.
     */
    public function definition() {
        $learningobject = optional_param('lb', 0, PARAM_INT);
        $mform = $this->_form;

        $options = [
            0 => get_string('course', 'local_edudashboard'),
            1 => get_string('program', 'local_edudashboard'),
        ];

        // Add select element for learning object.
        $select = $mform->addElement('select', 'learningobject', get_string('learningobject', 'local_edudashboard'), $options, [
            'id' => 'learningobject_select',
        ]);
        $select->setSelected($learningobject);

        $mform->disable_form_change_checker();
    }

    /**
     * Custom validation for the form.
     *
     * @param array $data Form data
     * @param array $files Form files
     * @return array Validation errors
     */
    public function validation($data, $files) {
        return [];
    }
}
