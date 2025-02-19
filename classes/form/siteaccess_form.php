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

namespace local_edudashboard\output;

use local_edudashboard\extra\course_report;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once("$CFG->libdir/formslib.php");

class siteaccess_form extends \moodleform
{
    //Add elements to form

    public function definition()
    {
        global $CFG;

        $courseid = optional_param('id', 1, PARAM_INT);

        $mform = $this->_form; // Don't forget the underscore!

        $courses = course_report::getSiteCourses(array(), false);

        foreach ($courses as $course) {

            $options[$course->id] = $course->fullname;

            if ($course->id == 1) {
                $options[$course->id] = $course->fullname . " (Site)";
            } //We dont want forn page
            else {
                $options[$course->id] = $course->fullname . " ({$course->shortname})";
            }



        }

    
        $select = $mform->addElement('select', 'courses', get_string('selectedcourse', 'local_edudashboard'), $options, array("id" => "course_select"));

        $mform->addElement('date_selector', 'fromdate', get_string('fromdate', 'local_edudashboard'), array(
            'optional' => true
        ), array("id" => "timefrom"));

        $mform->addElement('date_selector', 'todate', get_string('todate', 'local_edudashboard'), array(
            'stopyear' => intval(date("Y")),
            'optional' => true
        ), array("id" => "timeto"));
        // This will select the colour blue.
        $select->setSelected(intval($courseid));

        $mform->addElement('button', 'intro',  get_string('tofilter', 'local_edudashboard'), array("id"=>"access_filter"));


        $mform->disable_form_change_checker();

    }
    //Custom validation should be added here
    function validation()
    {
        return array();
    }
}
