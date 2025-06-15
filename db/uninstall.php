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
 * Theme custom uninstall.
 *
 * @package   theme_boost_training
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Theme_boost_training uninstall function.
 *
 * @return void
 * @throws Exception
 */
function xmldb_theme_boost_training_uninstall() {
    unset_config("startcolor", "theme_boost_training");

    unset_config("background_profile_image", "theme_boost_training");

    unset_config("backgroundimage", "theme_boost_training");
    unset_config("loginbackgroundimage", "theme_boost_training");
    unset_config("unaddableblocks", "theme_boost_training");

    unset_config("scsspre", "theme_boost_training");
    unset_config("scss", "theme_boost_training");

    unset_config("enable_accessibility", "theme_boost_training");
    unset_config("enable_vlibras", "theme_boost_training");
    unset_config("course_summary", "theme_boost_training");

    unset_config("footer_background_color", "theme_boost_training");
    unset_config("footer_title_1", "theme_boost_training");
    unset_config("footer_html_1", "theme_boost_training");
    unset_config("footer_title_2", "theme_boost_training");
    unset_config("footer_html_2", "theme_boost_training");
    unset_config("footer_title_3", "theme_boost_training");
    unset_config("footer_html_3", "theme_boost_training");
    unset_config("footer_title_4", "theme_boost_training");
    unset_config("footer_html_4", "theme_boost_training");

    unset_config("footer_show_copywriter", "theme_boost_training");
}
