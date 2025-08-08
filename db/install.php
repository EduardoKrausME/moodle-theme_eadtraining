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
 * Theme custom Installation.
 *
 * @package   theme_eadtraining
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Theme_eadtraining install function.
 *
 * @return void
 * @throws Exception
 */
function xmldb_theme_eadtraining_install() {
    global $DB, $CFG;

    if (file_exists("{$CFG->dirroot}/theme/boost_training/version")) {
        $configs = $DB->get_records_sql("SELECT * FROM {config_plugins} WHERE plugin LIKE 'theme_boost_eadtraining'");
        foreach ($configs as $config) {
            set_config($config->name, $config->name, "theme_eadtraining");
        }

        $files = $DB->get_records_sql("SELECT * FROM {files} WHERE component LIKE 'theme_boost_eadtraining'");
        foreach ($files as $file) {
            unset($file->id);
            $file->component = "theme_eadtraining";
            $file->filearea = str_replace("boost_training", "eadtraining", $file->filearea);
            $file->pathnamehash =
                sha1("/{$file->contextid}/{$file->component}/{$file->filearea}/{$file->itemid}{$file->filepath}{$file->filename}");
            $DB->insert_record("files", $file);
        }
    } else {
        // Profile background image.
        $fs = get_file_storage();
        $filerecord = [
            "component" => "theme_eadtraining",
            "contextid" => context_system::instance()->id,
            "userid" => get_admin()->id,
            "filearea" => "background_profile_image",
            "filepath" => "/",
            "itemid" => 0,
            "filename" => "user-modal-background.jpg",
        ];
        $fs->create_file_from_pathname($filerecord, "{$CFG->dirroot}/theme/eadtraining/pix/user-modal-background.jpg");
        set_config("background_profile_image", "/user-modal-background.jpg", "theme_eadtraining");
        set_config("brandcolor_background_menu", 0, "theme_eadtraining");

        set_config("backgroundimage", "", "theme_eadtraining");
        set_config("loginbackgroundimage", "", "theme_eadtraining");

        set_config("scsspre", "", "theme_eadtraining");
        set_config("scss", "", "theme_eadtraining");

        set_config("course_summary", 0, "theme_eadtraining");
        set_config("course_summary_banner", 0, "theme_eadtraining");
        set_config("course_summary_banner_position", 0, "theme_eadtraining");
        set_config("svg_animate", 1, "theme_eadtraining");

        set_config("enable_accessibility", 0, "theme_eadtraining");
        set_config("enable_vlibras", 0, "theme_eadtraining");

        set_config("footer_background_color", "", "theme_eadtraining");
        set_config("footer_title_1", "", "theme_eadtraining");
        set_config("footer_html_1", "", "theme_eadtraining");
        set_config("footer_title_2", "", "theme_eadtraining");
        set_config("footer_html_2", "", "theme_eadtraining");
        set_config("footer_title_3", "", "theme_eadtraining");
        set_config("footer_html_3", "", "theme_eadtraining");
        set_config("footer_title_4", "", "theme_eadtraining");
        set_config("footer_html_4", "", "theme_eadtraining");

        set_config("footer_show_copywriter", 1, "theme_eadtraining");
    }
}
