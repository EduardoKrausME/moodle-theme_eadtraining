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
 * Course file
 *
 * @package   theme_boost_training
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_boost_training;

use moodle_url;

/**
 * Class course
 *
 * @package theme_boost_training
 */
class course {
    /**
     * Function show_image_top_course
     *
     * @param \stdClass $course
     *
     * @return bool
     *
     * @throws \Exception
     */
    public static function show_image_top_course($course) {
        global $DB;

        $sql = "
            SELECT cd.*
              FROM {customfield_data}  cd
              JOIN {customfield_field} cf ON cf.id = cd.fieldid
             WHERE cd.shortname  = 'show_image_top_course'
               AND cd.instanceid = :courseid";
        $data = $DB->get_record_sql($sql, ["courseid" => $course->id]);

        if (!isset($data->intvalue)) {
            $data = (object)["intvalue" => 0];
        }

        // Marcou não nas configurações.
        if ($data->intvalue == 2) {
            return false;
        }
        // Marcado (vazio) nas configurações.
        if ($data->intvalue == 0) {
            return theme_boost_training_setting_file_url("background_course_image");
        }

        $sql = "
            SELECT cd.*
              FROM {customfield_data}  cd
              JOIN {customfield_field} cf ON cf.id = cd.fieldid
             WHERE cd.shortname  = 'background_course_image'
               AND cd.instanceid = :courseid";
        $customfielddata = $DB->get_record_sql($sql, ["courseid" => $course->id]);
        if ($customfielddata) {
            $sql = "
                SELECT contextid, itemid, filename
                  FROM {files}
                 WHERE component = 'customfield_picture'
                   AND filearea  = 'file'
                   AND itemid    = :itemid
                   AND filesize  > 10";
            $file = $DB->get_record_sql($sql, ["itemid" => $customfielddata->id]);
            if ($file) {
                return moodle_url::make_pluginfile_url($file->contextid, "customfield_picture", "file",
                    $file->itemid, "/", $file->filename)->out(true);
            }
        }

        if ($backgroundurl = theme_boost_training_setting_file_url("background_course_image")) {
            return $backgroundurl;
        }

        if ($data->intvalue == 1) {
            $sql = "
                SELECT *
                  FROM {files}
                 WHERE contextid   = :contextid
                   AND component   = 'course'
                   AND filearea    = 'overviewfiles'
                   AND mimetype LIKE 'image%'
                LIMIT 1";
            $coursefile = $DB->get_record_sql($sql, ["contextid" => \context_course::instance($course->id)->id]);
            if ($coursefile) {
                global $CFG;
                return "{$CFG->wwwroot}/pluginfile.php/{$coursefile->contextid}/course/overviewfiles/{$coursefile->filename}";
            }
        }

        return false;
    }
}
