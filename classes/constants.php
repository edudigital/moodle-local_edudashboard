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
 * Defines all constants for use in the EDUdashboard plugin.
 *
 * @package     local_edudashboard
 * @category    admin
 * @copyright   2025 edudigital <geral@edudigital-learn.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* Course completion constants */
define('LOCAL_EDUDASHBOARD_COURSE_COMPLETE_00PER', 0);
define('LOCAL_EDUDASHBOARD_COURSE_COMPLETE_20PER', 0.2);
define('LOCAL_EDUDASHBOARD_COURSE_COMPLETE_40PER', 0.4);
define('LOCAL_EDUDASHBOARD_COURSE_COMPLETE_60PER', 0.6);
define('LOCAL_EDUDASHBOARD_COURSE_COMPLETE_80PER', 0.8);
define('LOCAL_EDUDASHBOARD_COURSE_COMPLETE_100PER', 1);

/* Percentage constants */
define('LOCAL_EDUDASHBOARD_PERCENTAGE_00', "0%");
define('LOCAL_EDUDASHBOARD_PERCENTAGE_20', "20%");
define('LOCAL_EDUDASHBOARD_PERCENTAGE_40', "40%");
define('LOCAL_EDUDASHBOARD_PERCENTAGE_60', "60%");
define('LOCAL_EDUDASHBOARD_PERCENTAGE_80', "80%");
define('LOCAL_EDUDASHBOARD_PERCENTAGE_100', "100%");

/* Time constants */
define('LOCAL_EDUDASHBOARD_ONEDAY', 24 * 60 * 60);
define('LOCAL_EDUDASHBOARD_ONEWEEK', 7 * 24 * 60 * 60);
define('LOCAL_EDUDASHBOARD_ONEMONTH', 30 * 24 * 60 * 60);
define('LOCAL_EDUDASHBOARD_ONEYEAR', 365 * 24 * 60 * 60);
define('LOCAL_EDUDASHBOARD_ALL', "all");
define('LOCAL_EDUDASHBOARD_WEEKLY', "weekly");
define('LOCAL_EDUDASHBOARD_MONTHLY', "monthly");
define('LOCAL_EDUDASHBOARD_YEARLY', "yearly");
define('LOCAL_EDUDASHBOARD_WEEKLY_DAYS', 7);
define('LOCAL_EDUDASHBOARD_MONTHLY_DAYS', 30);
define('LOCAL_EDUDASHBOARD_YEARLY_DAYS', 365);

/* Email schedule constants */
define('LOCAL_EDUDASHBOARD_ESR_DAILY_EMAIL', 0);
define('LOCAL_EDUDASHBOARD_ESR_WEEKLY_EMAIL', 1);
define('LOCAL_EDUDASHBOARD_ESR_MONTHLY_EMAIL', 2);

/* Email time constants */
define('LOCAL_EDUDASHBOARD_ESR_0630AM', 0);
define('LOCAL_EDUDASHBOARD_ESR_1000AM', 1);
define('LOCAL_EDUDASHBOARD_ESR_0430PM', 2);
define('LOCAL_EDUDASHBOARD_ESR_1030PM', 3);

/* Block type constants */
define('LOCAL_EDUDASHBOARD_BLOCK_TYPE_DEFAULT', 0);
define('LOCAL_EDUDASHBOARD_BLOCK_TYPE_CUSTOM', 1);

/* Block view constants */
define('LOCAL_EDUDASHBOARD_BLOCK_DESKTOP_VIEW', 'desktopview');
define('LOCAL_EDUDASHBOARD_BLOCK_TABLET_VIEW', 'tabletview');
define('LOCAL_EDUDASHBOARD_BLOCK_LARGE', 2);
define('LOCAL_EDUDASHBOARD_BLOCK_MEDIUM', 1);
define('LOCAL_EDUDASHBOARD_BLOCK_SMALL', 0);

/* Course Progress Manager constants */
define('LOCAL_EDUDASHBOARD_CPM_STUDENTS_ARCHETYPE', 'student');

/* Color themes constant */
define('LOCAL_EDUDASHBOARD_COLOR_THEMES', [
    ['#F98012', '#133F3F', '#00A1A8', '#444444', '#666666'],
    ['#AC0034', '#142458', '#DFC11C', '#333333', '#999999'],
    ['#ED553B', '#20639B', '#92CD53', '#222222', '#888888'],
]);

/* Upgrade URL constant */
define('LOCAL_EDUDASHBOARD_UPGRADE_URL', 'https://edudigital.pt');
