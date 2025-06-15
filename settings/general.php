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
 * General file
 *
 * @package   theme_boost_training
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$page = new admin_settingpage("theme_boost_training_general", get_string("generalsettings", "theme_boost_training"));

// We use an empty default value because the default colour should come from the preset.
$name = 'theme_boost/brandcolor';
$title = get_string('brandcolor', 'theme_boost');
$description = get_string('brandcolor_desc', 'theme_boost');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Background image setting.
$name = "theme_boost_training/backgroundimage";
$title = get_string("backgroundimage", "theme_boost_training");
$description = get_string("backgroundimage_desc", "theme_boost_training");
$setting = new admin_setting_configstoredfile($name, $title, $description, "backgroundimage");
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

// Login Background image setting.
$name = "theme_boost_training/loginbackgroundimage";
$title = get_string("loginbackgroundimage", "theme_boost_training");
$description = get_string("loginbackgroundimage_desc", "theme_boost_training");
$setting = new admin_setting_configstoredfile($name, $title, $description, "loginbackgroundimage");
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

// Unaddable blocks.
// Blocks to be excluded when this theme is enabled in the "Add a block" list: Administration, Navigation, Courses and
// Section links.
$default = "navigation,settings,course_list,section_links";
$setting = new admin_setting_configtext("theme_boost_training/unaddableblocks",
    get_string("unaddableblocks", "theme_boost_training"),
    get_string("unaddableblocks_desc", "theme_boost_training"), $default, PARAM_TEXT);
$page->add($setting);

$settings->add($page);
