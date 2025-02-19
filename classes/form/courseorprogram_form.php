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

class courseorprogram_form extends \moodleform
{
    //Add elements to form

    public function definition()
    {

        $learningobject = optional_param('lb', 0, PARAM_INT);

        $mform = $this->_form; // Don't forget the underscore!

        $options[0] = get_string('course', 'local_edudashboard');

        $options[1] = get_string('program', 'local_edudashboard');;

        $select = $mform->addElement('select', 'learningobject', get_string('learningobject', 'local_edudashboard'), $options, array("id" => "learningobject_select"));
        // This will select the colour blue.
        $select->setSelected($learningobject);


        $mform->disable_form_change_checker();

    }
    //Custom validation should be added here
    function validation()
    {
        return array();
    }
}