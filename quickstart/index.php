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
 * view file
 *
 * @package   theme_boost_training
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_boost_training\editor\editor_tiny;
use theme_boost_training\images\git;

require_once("../../../config.php");
global $CFG, $PAGE, $OUTPUT, $DB, $USER;
require_admin();

if (optional_param("POST", false, PARAM_INT)) {
    require_sesskey();

    // Save configs.
    $configkeys = [
        "homemode" => PARAM_INT,
        "course_summary" => PARAM_INT,
        "banner_image" => PARAM_ALPHANUMEXT,
        "svg_animate" => PARAM_BOOL,
        "enable_accessibility" => PARAM_BOOL,
        "enable_vlibras" => PARAM_BOOL,
        "footer_background_color" => PARAM_RAW, // Hex color.
        "footer_title_1" => PARAM_TEXT,
        "footer_html_1" => PARAM_RAW,
        "footer_title_2" => PARAM_TEXT,
        "footer_html_2" => PARAM_RAW,
        "footer_title_3" => PARAM_TEXT,
        "footer_html_3" => PARAM_RAW,
        "footer_title_4" => PARAM_TEXT,
        "footer_html_4" => PARAM_RAW,
    ];
    foreach ($configkeys as $name => $type) {
        $value = optional_param($name, false, $type);
        if ($value) {
            set_config($name, $value, "theme_boost_training");
        }
    }

    // Save banners home.
    require_once("../_editor/editor-lib.php");
    $pages = $DB->get_records("theme_boost_training_pages", ["local" => "home"]);
    $homemodebanners = optional_param_array("homemode_banners", false, PARAM_TEXT);
    foreach ($homemodebanners as $template) {
        $located = false;
        foreach ($pages as $page) {
            if (isset($page->template[3]) && $page->template == $template) {
                $located = true;
            }
        }

        if (!$located) {
            try {
                editor_create_page($template, $USER->lang, "home");
            } catch (Exception $e) { // phpcs:disable
            }
        }
    }

    // Upload files.
    require_once("{$CFG->libdir}/filelib.php");
    $filefields = [
        "logocompact" => "core",
        "favicon" => "core",
        "banner_file" => "theme_boost_training",
        "background_profile_image" => "theme_boost_training",
    ];

    $fs = get_file_storage();
    $syscontext = context_system::instance();
    foreach ($filefields as $fieldname => $component) {
        if (!empty($_FILES[$fieldname]) && is_uploaded_file($_FILES[$fieldname]["tmp_name"])) {
            // Delete old files (if you want to keep a single file).
            $fs->delete_area_files($syscontext->id, $component, $fieldname, 0);
            $filename = clean_param($_FILES[$fieldname]["name"], PARAM_FILE);
            $filerecord = [
                "contextid" => $syscontext->id,
                "component" => $component,
                "filearea" => $fieldname,
                "itemid" => 0,
                "filepath" => "/",
                "filename" => $filename,
            ];

            // Save the new file.
            $fs->create_file_from_pathname($filerecord, $_FILES[$fieldname]["tmp_name"]);
        }
    }

    if (optional_param("homemode", false, PARAM_INT)) {
        $USER->editing = true;
    }
    redirect("/", get_string("quickstart_banner-saved", "theme_boost_training"));
}

$PAGE->set_context(context_system::instance());
$PAGE->set_url("/theme/boost_training/quickstart/index.php#home");
$PAGE->set_title(get_string("quickstart_title", "theme_boost_training"));
$PAGE->set_heading(get_string("quickstart_title", "theme_boost_training"));

$PAGE->requires->css("/theme/boost_training/quickstart/style.css");
$PAGE->requires->js("/theme/boost_training/quickstart/script.js");
$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin("ui");

require("{$CFG->libdir}/editor/tiny/lib.php");
$editor = new editor_tiny();
$editor->head_setup();

echo $OUTPUT->header();

echo '<form class="quickstart-content" method="post" enctype="multipart/form-data">';
echo '<input type="hidden" name="POST" value="1" />';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';

// Home.
$pages = $DB->get_records("theme_boost_training_pages", ["local" => "home"]);
$templates = [];
foreach ($pages as $page) {
    if (isset($page->template[3])) {
        $templates[$page->template] = true;
    }
}
$homemustache = [
    "homemode" => get_config("theme_boost_training", "homemode"),
    "templates" => $templates,
    "next" => "courses",
];
echo $OUTPUT->render_from_template("theme_boost_training/quickstart/home", $homemustache);

// Course.
$page = $DB->get_record("theme_boost_training_pages", ["local" => "all-courses"]);
$template = "";
if ($page) {
    if (isset($page->template[3])) {
        $template = $page->template;
    }
}
$coursesmustache = [
    "svg_animate" => get_config("theme_boost_training", "svg_animate"),
    "course_summary_0" => get_config("theme_boost_training", "course_summary") == 0,
    "course_summary_1" => get_config("theme_boost_training", "course_summary") == 1,
    "course_summary_2" => get_config("theme_boost_training", "course_summary") == 2,
    "banners" => git::list_all("banner", $template),
    "banner_file_extensions" => "PNG, JPG",
    "return" => "home",
    "next" => "logos",
];
echo $OUTPUT->render_from_template("theme_boost_training/quickstart/courses", $coursesmustache);
$PAGE->requires->js_call_amd("theme_boost_training/default_image", "generateimage", ["svg-courseid-111", 111, true]);
$PAGE->requires->js_call_amd("theme_boost_training/default_image", "generateimage", ["svg-courseid-222", 222, false]);

// Logos.
$logosmustache = [
    "return" => "courses",
    "next" => "user-profile",
    "logocompact_url" => $OUTPUT->get_compact_logo_url(300, 300),
    "logocompact_extensions" => "PNG, SVG, JPG",
    "favicon_url" => $OUTPUT->favicon(),
    "favicon_extensions" => "PNG, SVG, JPG",
];
echo $OUTPUT->render_from_template("theme_boost_training/quickstart/logos", $logosmustache);

// User profile.
$usermustache = [
    "background_profile_image_url" => theme_boost_training_setting_file_url("background_profile_image")->out(),
    "background_profile_image_extensions" => "PNG, JPG",
    "return" => "logos",
    "next" => "accessibility",
];
echo $OUTPUT->render_from_template("theme_boost_training/quickstart/user-profile", $usermustache);

// Accessibility.
$accessibilitymustache = [
    "enable_accessibility" => get_config("theme_boost_training", "enable_accessibility"),
    "lang_has_ptbr" => $CFG->lang == "pt_br",
    "enable_vlibras" => get_config("theme_boost_training", "enable_vlibras"),
    "return" => "user-profile",
    "next" => "footer",
];
echo $OUTPUT->render_from_template("theme_boost_training/quickstart/accessibility", $accessibilitymustache);

// Footer.
$htmlselect = "";
foreach (theme_boost_training_colors() as $color) {
    $htmlselect .= "\n\n" . $OUTPUT->render_from_template("theme_boost_training/settings/color", [
            "background" => $color,
            "footercolor" => true,
            "color" => $color,
        ]);
}
$footermustache = [
    "footer_background_color" => get_config("theme_boost_training", "footer_background_color"),
    "htmlselect" => $htmlselect,
    "blocks" => [
        [
            "num" => 1,
            "active" => true,
            "footer_title" => get_config("theme_boost_training", "footer_title_1"),
            "footer_html" => get_config("theme_boost_training", "footer_html_1"),
        ],
        [
            "num" => 2,
            "footer_title" => get_config("theme_boost_training", "footer_title_2"),
            "footer_html" => get_config("theme_boost_training", "footer_html_2"),
        ],
        [
            "num" => 3,
            "footer_title" => get_config("theme_boost_training", "footer_title_3"),
            "footer_html" => get_config("theme_boost_training", "footer_html_3"),
        ],
        [
            "num" => 4,
            "footer_title" => get_config("theme_boost_training", "footer_title_4"),
            "footer_html" => get_config("theme_boost_training", "footer_html_4"),
        ],
    ],
    "tyni_editor_config" => $editor->tyni_editor_config(),
    "return" => "accessibility",
];
echo $OUTPUT->render_from_template("theme_boost_training/quickstart/footer", $footermustache);
$PAGE->requires->js_call_amd("theme_boost_training/settings", "minicolors", ["id_footer_background_color"]);

echo '</form>';

echo $OUTPUT->footer();
