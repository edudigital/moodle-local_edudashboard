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

defined('MOODLE_INTERNAL') || die();

/**
 * Edudashboard report renderer
 */
class local_edudashboard_renderer extends plugin_renderer_base {
    /**
     * Renders the couse bundle view page.
     * @param  \local_edudashboard\output\edwiserreports_renderable $report Object of Edwiser Reports renderable class
     * @return string  Html Structure of the view page
     */
    public function render_edudashboard(\local_edudashboard\output\edudashboard_renderable $report) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_edudashboard/edudashboard', $templatecontext);
    }

    public function render_categoriesoverview(\local_edudashboard\output\categoriesoverview_renderable $report) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_edudashboard/categoriesoverview', $templatecontext);
    }

    public function render_siteoverview(\local_edudashboard\output\siteoverview_renderable $report) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_edudashboard/siteoverview', $templatecontext);
    }

    public function render_studentcourseoverview(\local_edudashboard\output\studentcourseoverview_renderable $report) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_edudashboard/studentcourseoverview', $templatecontext);
    }

    public function render_sitecompletion(\local_edudashboard\output\sitecompletion_renderable $report) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_edudashboard/sitecompletion', $templatecontext);
    }

    public function render_userdossie(\local_edudashboard\output\userdossie_renderable $report) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_edudashboard/userdossie', $templatecontext);
    }

    public function render_userreport(\local_edudashboard\output\userreport_renderable $report) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_edudashboard/userreport', $templatecontext);
    }

    public function render_pagesreport(\local_edudashboard\output\pagesreport_renderable $report) {
        $templatecontext = $report->export_for_template($this);
        $template_name = $report->report_type === 'authentication' ? 'authenticationreport' : 'coursereport';
        return $this->render_from_template("local_edudashboard/$template_name", $templatecontext);        
    }

    public function render_usergradeavg(\local_edudashboard\output\usergradeavg_renderable $report) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_edudashboard/usergradeavg', $templatecontext);
    }

    public function render_authentication(\local_edudashboard\output\authentication_renderable $report) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_edudashboard/authentication', $templatecontext);
    }

    public function render_coursessize(\local_edudashboard\output\coursessize_renderable $report) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_edudashboard/coursessize', $templatecontext);
    }


}

