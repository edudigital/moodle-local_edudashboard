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
 * Reports block external apis
 *
 * @package     local_edudashboard
 * @copyright   2025 edudigital <geral@edudigital-learn.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edudashboard\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_value;
use local_edudashboard\extra\util;

require_once($CFG->libdir.'/externallib.php');
require_once($CFG->dirroot . "/local/edudashboard/classes/constants.php");
if(util::isTotara()){
    require_once($CFG->dirroot.'/totara/program/lib.php');  
  }
/**
 * Trait implementing the external function local_edudashboard_set_plugin_config.
 */
trait get_sitecoursereport {

    /**
     * Describes the structure of parameters for the function.
     *
     * @return external_function_parameters
     */
    public static function get_sitecoursereport_parameters() {
        return new \external_function_parameters(
            array (
                'learnobject' => new \external_value(PARAM_INT, get_string('learnobject', 'local_edudashboard')),
            )
        );
    }

    /**
     * Set plugin configuration
     *
     * @param  string $pluginname Plugin name
     * @param  string $configname Configuration name
     * @return object             COnfiguration
     */
    public static function get_sitecoursereport($learnobject) {
        // Get Plugin config.

        global $DB;
        $progs = [];
        if(util::isTotara()){
            $progs = prog_get_programs();
          
            foreach ($progs as $key => $prog) {
              $res = util::get_program_learners($prog->id,false);
          
              foreach ($res as $key2 => $user) {
                $res[$key2]->progress = totara_program_get_user_percentage_complete(1, $user->id);
                $res[$key2]->iscompleted = prog_is_complete(1, $user->id);
              }
              $progs[$key]->learners = $res;  
              
            }
           // print_object(prog_get_courses_associated_with_programs());
          }
    
        return array('learnobject' =>$progs);
    }
    /**
     * Describes the structure of the function return value.
     *
     * @return external_single_structure
     */
    public static function get_sitecoursereport_returns() {
        return new \external_single_structure(
            array(
                'learnobject' => new \external_value(PARAM_RAW, 'Site Login', null),
            )
        );
    }
}
