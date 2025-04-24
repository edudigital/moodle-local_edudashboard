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
 * Privacy provider for the local_edudashboard plugin.
 *
 * @package    local_edudashboard
 * @category   admin
 * @copyright  2025 edudigital <geral@edudigital-learn.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edudashboard\privacy;

defined('MOODLE_INTERNAL') || die();

// Debug: Confirm that this file is being loaded.
debugging('local_edudashboard privacy provider.php loaded', DEBUG_DEVELOPER);

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_privacy\local\request\core_user_data_provider;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;

/**
 * Privacy provider for the local_edudashboard plugin.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_user_data_provider,
    \core_privacy\local\request\userlist_provider {

    /**
     * Returns metadata about the data this plugin accesses.
     *
     * @param collection $collection The initialized collection to add items to.
     * @return collection The updated collection of metadata items.
     */
    public static function get_metadata(collection $collection): collection {
        // User Insights.
        $collection->link_subsystem('core_user', 'privacy:metadata:core_user');

        // Course Analytics.
        $collection->link_subsystem('core_course', 'privacy:metadata:core_course');

        // Performance Monitoring.
        $collection->link_subsystem('core_logstore', 'privacy:metadata:core_logstore');

        // Completions activities.
        $collection->link_subsystem('core_completion', 'privacy:metadata:core_completion');

        // Role-Based Access.
        $collection->link_subsystem('core_role', 'privacy:metadata:core_role');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist The contextlist object.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        // The plugin generates reports based on user activity, which can include:
        // - System context (authentication logs).
        // - Course contexts (course completions, activities).
        $sql = "SELECT DISTINCT c.id
                FROM {context} c
                JOIN {course} course ON course.id = c.instanceid AND c.contextlevel = :courselevel
                JOIN {course_completions} cc ON cc.course = course.id
                WHERE cc.userid = :userid
                UNION
                SELECT c.id
                FROM {context} c
                JOIN {role_assignments} ra ON ra.contextid = c.id
                WHERE ra.userid = :userid2
                UNION
                SELECT c.id
                FROM {context} c
                WHERE c.contextlevel = :systemlevel";

        $params = [
            'courselevel' => CONTEXT_COURSE,
            'systemlevel' => CONTEXT_SYSTEM,
            'userid' => $userid,
            'userid2' => $userid,
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export all user data for the specified user in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();
        $userid = $user->id;

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel == CONTEXT_SYSTEM) {
                $logs = $DB->get_records('logstore_standard_log', ['userid' => $userid], 'timecreated ASC');
                $logdata = [];
                foreach ($logs as $log) {
                    $logdata[] = [
                        'time' => transform::datetime($log->timecreated),
                        'action' => $log->action,
                        'ip' => $log->ip,
                    ];
                }
                writer::with_context($context)->export_data(
                    [get_string('authentication_report', 'local_edudashboard')],
                    (object) ['logs' => $logdata]
                );

                // Exportar dados de papéis de usuário.
                $roles = $DB->get_records('role_assignments', ['userid' => $userid]);
                $roledata = [];
                foreach ($roles as $role) {
                    $roledata[] = [
                        'roleid' => $role->roleid,
                        'contextid' => $role->contextid,
                        'timemodified' => transform::datetime($role->timemodified),
                    ];
                }
                writer::with_context($context)->export_data(
                    [get_string('role_based_access', 'local_edudashboard')],
                    (object) ['roles' => $roledata]
                );
            } else if ($context->contextlevel == CONTEXT_COURSE) {
                $courseid = $context->instanceid;
                $completion = $DB->get_record('course_completions', ['userid' => $userid, 'course' => $courseid]);
                $coursedata = [];
                if ($completion) {
                    $coursedata['completed'] = transform::yesno($completion->timecompleted > 0);
                    $completiontime = $completion->timecompleted ? transform::datetime($completion->timecompleted) : null;
                    $coursedata['completion_time'] = $completiontime;
                }

                $activities = $DB->get_records('course_modules_completion', ['userid' => $userid, 'coursemoduleid' => $courseid]);
                $activitydata = [];
                foreach ($activities as $activity) {
                    $activitydata[] = [
                        'activityid' => $activity->coursemoduleid,
                        'completed' => transform::yesno($activity->completionstate > 0),
                        'timecompleted' => $activity->timemodified ? transform::datetime($activity->timemodified) : null,
                    ];
                }

                writer::with_context($context)->export_data(
                    [get_string('course_report', 'local_edudashboard'), $courseid],
                    (object) [
                        'completion' => $coursedata,
                        'activities' => $activitydata,
                    ]
                );
            }
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // The plugin does not store data directly, so there is nothing to delete.
        // Accessed data (e.g. logs, conclusions) is managed by Moodle subsystems.
    }

    /**
     * Delete all user data for the specified user in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user to delete data for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        // The plugin does not store data directly, so there is nothing to delete.
        // Accessed data (e.g. logs, conclusions) is managed by Moodle subsystems.
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist to add the users to.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if ($context->contextlevel == CONTEXT_SYSTEM) {
            // Users with authentication logs in the system context.
            $sql = "SELECT DISTINCT userid
                    FROM {logstore_standard_log}
                    WHERE contextlevel = :contextlevel";
            $params = ['contextlevel' => CONTEXT_SYSTEM];
            $userlist->add_from_sql('userid', $sql, $params);

            // Users with assigned roles in the system context.
            $sql = "SELECT DISTINCT userid
                    FROM {role_assignments}
                    WHERE contextid = :contextid";
            $params = ['contextid' => $context->id];
            $userlist->add_from_sql('userid', $sql, $params);
        } else if ($context->contextlevel == CONTEXT_COURSE) {
            // Users with course completions in the course context.
            $sql = "SELECT DISTINCT cc.userid
                    FROM {course_completions} cc
                    JOIN {course} c ON c.id = cc.course
                    JOIN {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel
                    WHERE ctx.id = :contextid";
            $params = [
                'contextlevel' => CONTEXT_COURSE,
                'contextid' => $context->id,
            ];
            $userlist->add_from_sql('userid', $sql, $params);

            // Users with completed activities in the course context.
            $sql = "SELECT DISTINCT cmc.userid
                    FROM {course_modules_completion} cmc
                    JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
                    JOIN {course} c ON c.id = cm.course
                    JOIN {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel
                    WHERE ctx.id = :contextid";
            $params = [
                'contextlevel' => CONTEXT_COURSE,
                'contextid' => $context->id,
            ];
            $userlist->add_from_sql('userid', $sql, $params);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved userlist of users to delete data for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        // The plugin does not store data directly, so there is nothing to delete.
        // Accessed data (e.g. logs, conclusions) is managed by Moodle subsystems.
    }
}
