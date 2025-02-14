<?php

namespace local_edudashboard\output;

use local_edudashboard\extra\course_report;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once("$CFG->libdir/formslib.php");

class selectcourse_form extends \moodleform
{
    //Add elements to form

    public function definition()
    {
        global $CFG, $DB, $PAGE;

        $courseid = optional_param('id', 0, PARAM_INT);

        $mform = $this->_form; // Don't forget the underscore!

        $courses = course_report::getSiteCourses(array(), false);

        foreach ($courses as $course) {



            if ($course->id != 1) {
                $options[$course->id] = $course->fullname . " ({$course->shortname})";
            } //We dont want forn page

        }

        $action = $CFG->wwwroot;
        //print_object($action);
        $select = $mform->addElement('select', 'courses', get_string('selectedcourse', 'local_edudashboard'), $options, array("id" => "course_select"));
        // This will select the colour blue.
        $select->setSelected(intval($courseid));

        /*$mform->addElement('date_time_selector', 'assesstimestart', get_string('from'));

        $this->add_action_buttons(false, "Actualizar");*/

        $mform->disable_form_change_checker();

    }
    //Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }
}

class siteaccess_form extends \moodleform
{
    //Add elements to form

    public function definition()
    {
        global $CFG, $DB, $PAGE;

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

        $action = $CFG->wwwroot;
        //print_object($action);
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

        /*$mform->addElement('date_time_selector', 'assesstimestart', get_string('from'));

        $this->add_action_buttons(false, "Actualizar");*/

        $mform->disable_form_change_checker();

    }
    //Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }
}

class courseorprogram_form extends \moodleform
{
    //Add elements to form

    public function definition()
    {
        global $CFG, $DB, $PAGE;

        $learningobject = optional_param('lb', 0, PARAM_INT);

        $mform = $this->_form; // Don't forget the underscore!

        $options[0] = get_string('course', 'local_edudashboard');

        $options[1] = get_string('program', 'local_edudashboard');;

        // $action = $CFG->wwwroot;
        //print_object($action);
        $select = $mform->addElement('select', 'learningobject', get_string('learningobject', 'local_edudashboard'), $options, array("id" => "learningobject_select"));
        // This will select the colour blue.
        $select->setSelected($learningobject);

        /*$mform->addElement('date_time_selector', 'assesstimestart', get_string('from'));

        $this->add_action_buttons(false, "Actualizar");*/

        $mform->disable_form_change_checker();

    }
    //Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }
}