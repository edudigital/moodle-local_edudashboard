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

require_once('../../../config.php');
global $CFG;
require_once($CFG->libdir.'/dataformatlib.php');
global $DB;

require_login();


$dataformat = optional_param('dataformat', '', PARAM_ALPHA);

$columns = [
    'idnumber' => get_string('idnumber'),
];

$rs = $DB->get_recordset_sql(" SELECT * from {user} ", null, 0, $limitnum = 9);

download_as_dataformat('myfilename', $dataformat, $columns, $rs);

$rs->close();

header('Location: ' . $_SERVER['HTTP_REFERER']);

exit;
