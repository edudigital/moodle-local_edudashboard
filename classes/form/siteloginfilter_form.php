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

class siteloginfilter_form extends \moodleform
{
    //Add elements to form

    public function definition()
    {
        global $CFG ;

        $mform = $this->_form; // Don't forget the underscore!
 
        
        $mform->addElement('date_selector', 'login_fromdate', get_string('fromdate', 'local_edudashboard'), array(
            'optional' => true
        ), array("id" => "login_timefrom"));

        $mform->addElement('date_selector', 'login_todate', get_string('todate', 'local_edudashboard'), array(
            'stopyear' => intval(date("Y")),
            'optional' => true
        ), array("id" => "login_timeto"));
        
         $this->add_action_buttons(); 
      
        $mform->disable_form_change_checker();

    }
    //Custom validation should be added here
    function validation()
    {
        return array();
    }
}