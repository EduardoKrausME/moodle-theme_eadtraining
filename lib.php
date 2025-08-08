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
 * Theme functions.
 *
 * @package   theme_eadtraining
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @copyright based on work by 2016 Frédéric Massart - FMCorz.net
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Post process the CSS tree.
 *
 * @param string $tree The CSS tree.
 * @param theme_config $theme The theme config object.
 */
function theme_eadtraining_css_tree_post_processor($tree, $theme) {
    $prefixer = new theme_eadtraining\autoprefixer($tree);
    $prefixer->prefix();
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 *
 * @return string
 */
function theme_eadtraining_get_extra_scss($theme) {
    $content = "";
    $imageurl = $theme->setting_file_url("backgroundimage", "backgroundimage");

    // Sets the background image, and its settings.
    if (!empty($imageurl)) {
        $content .= "
            @media (min-width: 768px) {
                body {
                    background-image: url('$imageurl'); background-size: cover;
                 }
             }";
    }

    // Sets the login background image.
    $loginbackgroundimageurl = $theme->setting_file_url("loginbackgroundimage", "loginbackgroundimage");
    if (!empty($loginbackgroundimageurl)) {
        $content .= "
            body.pagelayout-login #page-wrapper {
                background-image: url('$loginbackgroundimageurl'); background-size: cover;
            }";
    }

    // Always return the background image with the scss when we have it.
    return !empty($theme->settings->scss) ? "{$theme->settings->scss}  \n  {$content}" : $content;
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 *
 * @return bool
 * @throws moodle_exception
 */
function theme_eadtraining_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        if (strpos($filearea, "editor_") === 0) {
            $fullpath = sha1("/{$context->id}/theme_eadtraining/{$filearea}/{$args[0]}/{$args[1]}");
            $fs = get_file_storage();
            if ($file = $fs->get_file_by_hash($fullpath)) {
                return send_stored_file($file, 0, 0, false, $options);
            }
        } else {
            $theme = theme_config::load("eadtraining");
            // By default, theme files must be cache-able by both browsers and proxies.
            if (!array_key_exists("cacheability", $options)) {
                $options["cacheability"] = "public";
            }
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        }
        send_file_not_found();
    } else if ($context->contextlevel == CONTEXT_MODULE) {
        $fullpath = sha1("/{$context->id}/theme_eadtraining/{$filearea}/{$args[0]}/{$args[1]}");
        $fs = get_file_storage();
        if ($file = $fs->get_file_by_hash($fullpath)) {
            return send_stored_file($file, 0, 0, false, $options);
        }
    } else {
        send_file_not_found();
    }
}

/**
 * Get the current user preferences that are available
 *
 * @return array[]
 */
function theme_eadtraining_user_preferences(): array {
    return [
        "drawer-open-block" => [
            "type" => PARAM_BOOL,
            "null" => NULL_NOT_ALLOWED,
            "default" => false,
            "permissioncallback" => [core_user::class, "is_current_user"],
        ],
        "drawer-open-index" => [
            "type" => PARAM_BOOL,
            "null" => NULL_NOT_ALLOWED,
            "default" => true,
            "permissioncallback" => [core_user::class, "is_current_user"],
        ],
    ];
}

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 *
 * @return string
 */
function theme_eadtraining_get_main_scss_content($theme) {
    global $CFG;
    return file_get_contents("{$CFG->dirroot}/theme/eadtraining/scss/style.scss");
}

/**
 * Get compiled css.
 *
 * @return string compiled css
 */
function theme_eadtraining_get_precompiled_css() {
    global $CFG;
    return file_get_contents("{$CFG->dirroot}/theme/eadtraining/scss/style.css");
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 *
 * @return string
 */
function theme_eadtraining_get_pre_scss($theme) {
    $scss = "";
    $configurable = [
        // Config key => [variableName, ...].
        "brandcolor" => ["primary"],
    ];

    $configboost = get_config("theme_boost");
    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($configboost->{$configkey}) ? $configboost->{$configkey} : null;
        if (empty($value)) {
            continue;
        }
        array_map(function ($target) use (&$scss, $value) {
            $scss .= "\${$target}: {$value};\n";
        }, (array)$targets);
    }

    $callbacks = get_plugins_with_function("theme_eadtraining_get_pre_scss");
    foreach ($callbacks as $plugintype => $plugins) {
        foreach ($plugins as $plugin => $callback) {
            if ($newscss = $callback($configurable)) {
                $scss = $newscss;
            }
        }
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;
}

/**
 * Function theme_eadtraining_progress_content
 *
 * @return array
 */
function theme_eadtraining_progress_content() {
    global $USER, $COURSE;

    $completion = new \completion_info($COURSE);

    // First, let's make sure completion is enabled.
    if (!$completion->is_enabled()) {
        return ["isprogress" => false];
    }

    if (!$completion->is_tracked_user($USER->id)) {
        return ["isprogress" => false];
    }

    // Before we check how many modules have been completed see if the course has.
    if ($completion->is_course_complete($USER->id)) {
        return [
            "isprogress" => true,
            "progress" => 100,
        ];
    }

    // Get the number of modules that support completion.
    $modules = $completion->get_activities();
    $count = count($modules);
    if (!$count) {
        return ["isprogress" => false];
    }

    // Get the number of modules that have been completed.
    $completed = 0;
    foreach ($modules as $module) {
        $data = $completion->get_data($module, true, $USER->id);
        if (($data->completionstate == COMPLETION_INCOMPLETE) || ($data->completionstate == COMPLETION_COMPLETE_FAIL)) {
            $completed += 0;
        } else {
            $completed += 1;
        };
    }

    return [
        "isprogress" => true,
        "progress" => intval(($completed / $count) * 100),
        "progress_completed" => $completed,
        "progress_count" => $count,
    ];
}

/**
 * Function theme_eadtraining_setting_file_url
 *
 * @param $setting
 *
 * @return bool|moodle_url
 *
 * @throws dml_exception
 */
function theme_eadtraining_setting_file_url($setting) {
    global $CFG;

    $filepath = get_config("theme_eadtraining", $setting);
    if (!$filepath) {
        return false;
    }
    $syscontext = context_system::instance();

    $url = moodle_url::make_file_url(
        "$CFG->wwwroot/pluginfile.php",
        "/{$syscontext->id}/theme_eadtraining/{$setting}/0/{$filepath}");

    return $url;
}

/**
 * theme_eadtraining_coursemodule_standard_elements
 *
 * @param moodleform_mod $formwrapper The moodle quickforms wrapper object.
 * @param MoodleQuickForm $mform The actual form object (required to modify the form).
 *
 * @throws coding_exception
 */
function theme_eadtraining_coursemodule_standard_elements(&$formwrapper, $mform) {
    static $executed = false;
    if ($executed) {
        return;
    }
    $executed = true;

    if ($formwrapper->get_current()->modulename == "label") {
        return;
    }
    if ($formwrapper->get_current()->modulename == "learningmap") {
        return;
    }

    global $CFG, $PAGE;
    if ($CFG->theme == "eadtraining" || $CFG->theme == "eadflix") {

        // Icones.
        $mform->addElement("header", "theme_eadtraining_icons",
            get_string("settings_icons_change_icons", "theme_eadtraining"));

        $filemanageroptions = [
            "accepted_types" => [".svg", ".png", ".jpg", ".jpeg"],
            "maxbytes" => -1,
            "maxfiles" => 1,
        ];

        // Background.
        if (isset($formwrapper->get_current()->coursemodule) && $formwrapper->get_current()->coursemodule) {
            $context = context_module::instance($formwrapper->get_current()->coursemodule);

            $draftitemid = file_get_submitted_draft_itemid("theme_eadtraining_customimage");
            file_prepare_draft_area(
                $draftitemid,
                $context->id,
                "theme_eadtraining", "theme_eadtraining_customimage", $formwrapper->get_current()->coursemodule);

            $formwrapper->set_data([
                "theme_eadtraining_customimage" => $draftitemid,
            ]);
        }
        $mform->addElement("filemanager", "theme_eadtraining_customimage",
            get_string("settings_icons_upload_image", "theme_eadtraining"),
            null, $filemanageroptions);

        $mform->addElement("static", "theme_eadtraining_custom", "",
            get_string("settings_icons_upload_image_desc", "theme_eadtraining"));

        // Icon.
        if (isset($formwrapper->get_current()->coursemodule) && $formwrapper->get_current()->coursemodule) {
            $context = context_module::instance($formwrapper->get_current()->coursemodule);

            $draftitemid = file_get_submitted_draft_itemid("theme_eadtraining_customicon");
            file_prepare_draft_area(
                $draftitemid,
                $context->id,
                "theme_eadtraining", "theme_eadtraining_customicon", $formwrapper->get_current()->coursemodule);

            $formwrapper->set_data([
                "theme_eadtraining_customicon" => $draftitemid,
            ]);
        }
        $filemanageroptions["accepted_types"] = [".svg", ".png"];
        $mform->addElement("filemanager", "theme_eadtraining_customicon",
            get_string("settings_icons_upload_icon", "theme_eadtraining"),
            null, $filemanageroptions);

        // Color.
        $mform->addElement("text", "theme_eadtraining_customcolor",
            get_string("settings_icons_color_icon", "theme_eadtraining"), []);
        $mform->setType("theme_eadtraining_customcolor", PARAM_TEXT);
        $PAGE->requires->js_call_amd("theme_eadtraining/settings", "minicolors",
            ["id_theme_eadtraining_customcolor"]);

        $mform->addElement("static", "theme_eadtraining_custom", "",
            get_string("settings_icons_color_icon_desc", "theme_eadtraining"));
    }
}

/**
 * Hook the add/edit of the course module.
 *
 * @param moodleform $data Data from the form submission.
 * @param stdClass $course The course.
 *
 * @return moodleform
 *
 * @throws coding_exception
 */
function theme_eadtraining_coursemodule_edit_post_actions($data, $course) {
    $context = context_module::instance($data->coursemodule);

    if (isset($data->theme_eadtraining_customimage)) {
        $options = ["subdirs" => true, "embed" => true];
        $filesave = file_save_draft_area_files($data->theme_eadtraining_customimage, $context->id,
            "theme_eadtraining", "theme_eadtraining_customimage", $data->coursemodule, $options);

        $name = "theme_eadtraining_customimage_{$data->coursemodule}";
        set_config($name, $filesave, "theme_eadtraining");

        \cache::make("theme_eadtraining", "css_cache")->purge();
    }

    if (isset($data->theme_eadtraining_customicon)) {
        $options = ["subdirs" => true, "embed" => true];
        $filesave = file_save_draft_area_files($data->theme_eadtraining_customicon, $context->id,
            "theme_eadtraining", "theme_eadtraining_customicon", $data->coursemodule, $options);

        $name = "theme_eadtraining_customicon_{$data->coursemodule}";
        set_config($name, $filesave, "theme_eadtraining");

        \cache::make("theme_eadtraining", "css_cache")->purge();
    }

    if (isset($data->theme_eadtraining_customcolor)) {
        $name = "theme_eadtraining_customcolor_{$data->coursemodule}";
        set_config($name, $data->theme_eadtraining_customcolor, "theme_eadtraining");

        \cache::make("theme_eadtraining", "css_cache")->purge();
    }

    return $data;
}

/**
 * List colors.
 *
 * @return array
 */
function theme_eadtraining_colors() {
    return [
        "#000428", // Azul Escuro.
        "#070000", // Preto.
        "#1a2a6c", // Azul Escuro.
        "#314755", // Cinza Escuro.
        "#007bc3", // Azul.
        "#007fff", // Azul Neon.
        "#00bf8f", // Verde Azulado.
        "#00c3b0", // Turquesa.
        "#30e8bf", // Verde Claro.
        "#83a4d4", // Azul Claro.
        "#7303c0", // Roxo.
        "#8000ff", // Roxo Neon.
        "#86377b", // Roxo Escuro.
        "#b21f1f", // Vermelho Escuro.
        "#c10f41", // Vermelho.
        "#d12924", // Vermelho Claro.
        "#fc354c", // Vermelho Claro.
        "#ff0000", // Vermelho Brilhante.
        "#ff007f", // Rosa Neon.
        "#ff00ff", // Magenta Brilhante.
        "#f55ff2", // Rosa.
        "#fd81b5", // Rosa Claro.
        "#ff512f", // Laranja Claro.
        "#e65c00", // Laranja.
        "#ff8000", // Laranja Neon.
        "#c99b10", // Amarelo Neon.
        "#997540", // Bege.
    ];
}

/**
 * Change color.
 *
 * @throws dml_exception
 */
function theme_eadtraining_change_color() {
    $config = get_config("theme_eadtraining");
    $configboost = get_config("theme_boost");

    if (isset($config->startcolor[5])) {
        $brandcolor = $config->startcolor;
    } else {
        $brandcolor = $configboost->brandcolor;
    }

    set_config("startcolor", "#000", "theme_eadtraining");
    set_config("footer_background_color", $brandcolor, "theme_eadtraining");

    theme_reset_all_caches();
}

if (!function_exists('str_starts_with')) {
    /**
     * The function returns {@see true} if the passed $haystack starts from the
     * $needle string or {@see false} otherwise.
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     * @since 8.0
     */
    function str_starts_with(string $haystack, string $needle): bool {
        return $needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
