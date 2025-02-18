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

 defined('MOODLE_INTERNAL') || die;

 class authenticationreport_table extends \table_sql {
 
     /**
     * Constructor
     * @param int $uniqueid all tables have to have a unique id, this is used
     *      as a key when storing table properties like sort order in the session.
     */
     function __construct($uniqueid) {
         parent::__construct($uniqueid);
 
         // Define the list of columns to show.
         $columns = array('id', 'lastaccess', 'lastip');
         $this->define_columns($columns);
 
         // Define the titles of columns to show in header.
         $headers = array(
             get_string('column_user', 'local_edudashboard'),
             get_string('column_datetime', 'local_edudashboard'),
             get_string('column_ip', 'local_edudashboard')
         );
         $this->define_headers($headers);
         
         $this->collapsible(true);
         $this->sortable(true);
         $this->pageable(true);
         $this->show_download_buttons_at(array(TABLE_P_BOTTOM));
     }
 

    /**
     * This function is called for each data row to allow processing of the
     * username value.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return username with link to profile or username only
     *     when downloading.
     */
    function col_id($values) {
        global $DB,$CFG;
        // If the data is being downloaded than we don't want to show HTML.
        $values = $DB->get_record('user', ['id' => intval($values->id)],"firstname,lastname,id,email");

        if ($this->is_downloading()) {
            return $values->username;
        } else {
            return '<a href="'.$CFG->wwwroot.'/local/edudashboard/userreport.php?id='.$values->id.'">'."{$values->firstname} {$values->lastname}</a>";
        }
    }

    function col_lastaccess($values) {
       //return \date( "d/m/Y H:i:s", $values->timecreated);
    
       if (!$this->is_downloading()) {
            $dateformat = get_string('strftimedatetime', 'core_langconfig');
        } else {
            $dateformat = get_string('strftimedatetimeshort', 'core_langconfig');
        }
         
        return userdate($values->lastaccess, $dateformat);
    }

    function col_lastip($values)
    {
        global $CFG;
        $ip = $values->lastip;

        if (empty($this->download)) {
            $url = new \moodle_url("$CFG->wwwroot/iplookup/index.php?ip={$ip}&user={$values->id}");
            $ip = $this->action_link($url, $ip, 'ip');
        }
        return $ip;
        //return '<a href="/iplookup/index.php?ip='.$values->ip.'&user='.$values->user.'">'.$values->ip.'</a>';
    }

     /**
     * This function is called for each data row to allow processing of
     * columns which do not have a *_cols function.
     * @return string return processed value. Return NULL if no change has
     *     been made.
     */
    function other_cols($colname, $value) {
        // For security reasons we don't want to show the password hash.

        if ($colname == 'password') {
            return "****";
        }
    }

    protected function action_link(\moodle_url $url, $text, $name = 'popup') {
        global $OUTPUT;
        $link = new \action_link($url, $text, new \popup_action('click', $url, $name, array('height' => 440, 'width' => 700)));
        return $OUTPUT->render($link);
    }

}