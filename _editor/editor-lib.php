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
 * Functions.
 *
 * @package   theme_boost_training
 * @copyright 2025 Eduardo kraus (http://eduardokraus.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Actionurl function
 *
 * @param $action
 * @return string
 */
function actionurl($action) {
    global $chave, $lang;
    return "actions.php?action={$action}&chave={$chave}&lang={$lang}&sesskey=" . sesskey();
}

/**
 * editor_create_page function
 *
 * @return void
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function editor_create_page() {
    global $CFG, $DB;

    $template = required_param("template", PARAM_TEXT);
    $lang = required_param("lang", PARAM_TEXT);
    $chave = required_param("chave", PARAM_TEXT);

    $infofile = __DIR__ . "/model/{$template}/info.json";
    if (file_exists($infofile)) {
        $info = json_decode(file_get_contents($infofile));
        $info->template = $template;
        $htmlfile = __DIR__ . "/model/{$template}/editor.html";
        $html = file_get_contents($htmlfile);

        $html = str_replace("src=\"../", "src=\"{$CFG->wwwroot}/theme/boost_training/_editor/model/{$template}/../", $html);
        $html = str_replace("url(\"../", "url(\"{$CFG->wwwroot}/theme/boost_training/_editor/model/{$template}/../", $html);
    } else {
        throw new Exception("File template not found");
    }

    $page = (object)["local" => $chave, "type" => $info->type, "title" => $info->title, "html" => $html, "info" => json_encode($info), "lang" => $lang, "sort" => time(),];
    $page->id = $DB->insert_record("theme_boost_training_pages", $page);
    redirect("{$CFG->wwwroot}/theme/boost_training/_editor/editor.php?dataid={$page->id}");
}

function compile_pages($pages) {
    global $PAGE;

    $returnpages = [];

    $previewdataid = optional_param("dataid", false, PARAM_INT);

    foreach ($pages as $page) {
        if ($page->id == $previewdataid) {
            $html = required_param("html", PARAM_RAW);
            $css = required_param("css", PARAM_RAW);
            if (isset($html[3])) {
                $html = preg_replace('/<\/?body.*?>/', '', $html);
                $page->html = "<div class='alert alert-warning page-editor-preview'>{$html}<style>{$css}</style></div>";
            }

            $savedata = boost_training_clear_params_array($_POST["save"], PARAM_RAW);
            $info = json_decode($page->info);
            $info->savedata = array_values($savedata);
            $page->info = json_encode($info);
        }

        if (isset($page->info[5])) {
            $info = json_decode($page->info);
            if ($info->type == "html-form" || $info->type == "form") {
                $file = __DIR__ . "/model/{$info->template}/create-block.php";
                if (file_exists($file)) {
                    require_once($file);

                    $createblocks = str_replace("-", "_", "{$info->template}_createblocks");
                    $block = $createblocks($page);

                    if (strpos($page->html, "[[change-to-blocks]]")) {
                        $page->html = str_replace("[[change-to-blocks]]", $block, $page->html);
                    } else {
                        $page->html = $page->html . $block;
                    }
                } else {
                    echo "{$file} not found<br>";
                }
            }

            if (isset($info->form->scripts)) {
                foreach ($info->form->scripts as $script) {
                    if ($script == "jquery") {
                        $PAGE->requires->jquery();
                    } elseif ($script == "jqueryui") {
                        $PAGE->requires->jquery_plugin("ui");
                    } elseif (strpos($script, "http") === 0) {
                        $PAGE->requires->js_init_code("require(['jquery'],function($){ $.getScript('{$script}')})");
                    } else {
                        if (file_exists(__DIR__ . "/model/{$info->template}/{$script}")) {
                            $PAGE->requires->js("/theme/boost_training/_editor/model/{$info->template}/{$script}");
                        }
                    }
                }
            }
            if (isset($info->form->styles)) {
                foreach ($info->form->styles as $style) {
                    if ($style == "bootstrap") {
                        // Theme already has bootstrap.
                    } else {
                        if (file_exists(__DIR__ . "/model/{$info->template}/{$style}")) {
                            $PAGE->requires->css("/theme/boost_training/_editor/model/{$info->template}/{$style}");
                        };
                    }
                }
            }
        }
        $returnpages[] = $page;
    }

    return $returnpages;
}

/**
 * I made clean_param_array recursive
 *
 * @param $in
 * @param $type
 * @return array|mixed
 */
function boost_training_clear_params_array($in, $type) {
    $out = [];
    if (is_array($in)) {
        foreach ($in as $key => $value) {
            $out[$key] = boost_training_clear_params_array($value, $type);
        }
    } elseif (is_string($in)) {
        try {
            return clean_param($in, $type);
        } catch (\coding_exception $e) {
            debugging($e->getMessage());
        }
    } else {
        return $in;
    }

    return $out;
}
