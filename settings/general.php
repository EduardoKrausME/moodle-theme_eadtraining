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

global $CFG, $OUTPUT, $PAGE;
require_once("{$CFG->dirroot}/theme/boost_training/lib.php");

$page = new admin_settingpage("theme_boost_training_general", get_string("generalsettings", "theme_boost_training"));
$htmlselect = "<link rel=\"stylesheet\" href=\"{$CFG->wwwroot}/theme/boost_training/scss/colors.css\" />";

$config = get_config("theme_boost_training");
if (!isset($config->startcolor[2])) {
    foreach (theme_boost_training_colors() as $color) {
        $htmlselect .= "\n\n" . $OUTPUT->render_from_template("theme_boost_training/settings/color", [
                "background" => $color,
                "startcolor" => true,
            ]);
    }

    $setting = new admin_setting_configtext("theme_boost_training/startcolor",
        get_string('brandcolor', 'theme_boost'),
        get_string('brandcolor_desc', 'theme_boost_training') . "<div class='mb-3'>{$htmlselect}</div>",
        "#1a2a6c");
    $PAGE->requires->js_call_amd("theme_boost_training/settings", "minicolors", [$setting->get_id()]);
    $setting->set_updatedcallback("theme_boost_training_change_color");
    $page->add($setting);
} else {
    foreach (theme_boost_training_colors() as $color) {
        $htmlselect .= "\n\n" . $OUTPUT->render_from_template("theme_boost_training/settings/color", [
                "background" => $color,
                "brandcolor" => true,
            ]);
    }

    // We use an empty default value because the default colour should come from the preset.
    $setting = new admin_setting_configtext("theme_boost/brandcolor",
        get_string('brandcolor', 'theme_boost'),
        get_string('brandcolor_desc', 'theme_boost_training') . "<div class='mb-3'>{$htmlselect}</div>",
        '#1a2a6c');
    $setting->set_updatedcallback("theme_boost_training_change_color");
    $page->add($setting);
    $PAGE->requires->js_call_amd("theme_boost_training/settings", "minicolors", [$setting->get_id()]);
}

$page->add(new admin_setting_configcheckbox("theme_boost_training/brandcolor_background_menu",
    get_string("brandcolor_background_menu", "theme_boost_training"),
    get_string("brandcolor_background_menu_desc", "theme_boost_training"), 0));

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
