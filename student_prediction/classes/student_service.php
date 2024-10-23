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

namespace local_student_prediction;

/**
 * Class student_service
 *
 * @package    local_student_prediction
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class student_service {
    public function get_students() {
        global $DB;
        return $DB->get_records_sql('
             SELECT u.id, 
                   u.firstname, 
                   u.lastname, 
                   g.finalgrade,
                   COUNT(DISTINCT s.id) AS total_assignments_submitted,
                   COUNT(DISTINCT d.id) AS total_forum_discussions,
                   SUM(CASE WHEN s.status = "SUBMITTED" THEN 1 ELSE 0 END) AS total_submitted,
                   AVG(TIMESTAMPDIFF(SECOND, s.timestarted, s.timemodified)) AS avg_submission_time
            FROM {user} u
            LEFT JOIN {grade_grades} g ON u.id = g.userid
            LEFT JOIN {assign_submission} s ON u.id = s.userid
            LEFT JOIN {forum_discussions} d ON d.userid = u.id  -- Asegúrate de que la relación es correcta
            GROUP BY u.id
        ');
    }
}
