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
 * Date condition.
 *
 * @package availability_coursetime
 * @copyright 2016 Valery Fremaux (valery.fremaux@gmail.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_coursetime;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');

/**
 * Week from course start condition.
 *
 * @package availability_coursetime
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {

    /** @var int Time (Unix epoch seconds) for condition. */
    private $courseid;
    private $timespent;

    /**
     * Constructor.
     *
     * @param \stdClass $structure Data structure from JSON decode
     * @throws \coding_exception If invalid data structure.
     */
    public function __construct($structure) {

        // Get course to check time.
        if (isset($structure->c)) {
            $this->courseid = $structure->c;
        } else {
            throw new \coding_exception('Missing or invalid ->c for course condition');
        }

        if (isset($structure->t)) {
            $this->timespent = $structure->t;
        } else {
            throw new \coding_exception('Missing or invalid ->t for time condition');
        }
    }

    public function save() {
        return (object)array('type' => 'coursetime',
                'c' => $this->courseid,
                't' => $this->timespent);
    }

    public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {
        global $DB, $CFG;

        $systemcontext = \context_system::instance();
        if (has_capability('moodle/site:config', $systemcontext)) {
            return true;
        }

        // Check condition.
        $now = self::get_time();

        if (!$course = $DB->get_record('course', array('id' => $this->courseid))) {
            return true;
        }

        $coursecontext = \context_course::instance($course->id);
        if (has_capability('moodle/course:manageactivities', $coursecontext)) {
            // People who can edit course do not need playing condition.
            return true;
        }

        require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
        $logs = use_stats_extract_logs($course->startdate, $now, $userid, $course->id);
        $aggregate = use_stats_aggregate_logs($logs, $course->startdate, $now);

        // Timespent stored in minutes.
        $allow = @$aggregate['coursetotal'][$course->id]->elapsed >= $this->timespent * 60;

        if ($not) {
            $allow = !$allow;
        }

        return $allow;
    }

    public function is_available_for_all($not = false) {
        global $CFG, $USER, $DB;

        // Check condition.
        $now = self::get_time();

        $course = $DB->get_record('course', array('id' => $this->courseid));

        $logs = use_stats_extract_logs($course->startdate, $now, $USER->id, $course->id);
        $aggregate = use_stats_aggregate_logs($logs, $course->startdate, $now);

        // Timespent stored in minutes.
        $allow = $aggregate['coursetotal'][$course->id]->elapsed >= $this->timespent * 60;

        if ($not) {
            $allow = !$allow;
        }

        return $allow;
    }

    public function get_description($full, $not, \core_availability\info $info) {
        return $this->get_either_description($not, false);
    }

    public function get_standalone_description(
            $full, $not, \core_availability\info $info) {
        return $this->get_either_description($not, true);
    }

    /**
     * Shows the description using the different lang strings for the standalone
     * version or the full one.
     *
     * @param bool $not True if NOT is in force
     * @param bool $standalone True to use standalone lang strings
     */
    protected function get_either_description($not, $standalone) {
        global $DB;

        $satag = $standalone ? 'short_' : 'full_';

        $course = $DB->get_record('course', array('id' => $this->courseid), 'id,shortname,fullname');
        $course->timespent = block_use_stats_format_time($this->timespent * 60);

        return get_string($satag . 'coursetime', 'availability_coursetime', $course);
    }

    /**
     * Gets time. This function is implemented here rather than calling time()
     * so that it can be overridden in unit tests. (Would really be nice if
     * Moodle had a generic way of doing that, but it doesn't.)
     *
     * @return int Current time (seconds since epoch)
     */
    protected static function get_time() {
        return time();
    }

    protected function get_debug_string() {
        return $this->timespent.' in '.$this->courseid;
    }

    public function update_after_restore($restoreid, $courseid, \base_logger $logger, $name) {
        return true;
    }
}
