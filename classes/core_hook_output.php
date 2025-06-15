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
 * Class core_hook_output
 *
 * @package   theme_boost_training
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_boost_training;

/**
 * Class core_hook_output
 *
 * @package theme_boost_training
 */
class core_hook_output {

    /**
     * Function before_footer_html_generation
     *
     * @throws \Exception
     */
    public static function before_footer_html_generation() {
        global $CFG, $DB, $COURSE, $SITE, $PAGE, $OUTPUT;

        $theme = $CFG->theme;
        if (isset($_SESSION["SESSION"]->theme)) {
            $theme = $_SESSION["SESSION"]->theme;
        }
        if ($theme != "boost_training") {
            return;
        }

        $css = "";
        if ($COURSE->id != $SITE->id) {

            $iconscss = "";

            $cache = \cache::make("theme_boost_training", "css_cache");
            $cachekey = "theme_boost_training_customimages_{$COURSE->id}";
            if (false && $cache->has($cachekey)) {
                $iconscss = $cache->get($cachekey);
            } else {
                // Backgrounds images modules.
                $sql = "
                    SELECT itemid, contextid, filename
                      FROM {files}
                     WHERE component LIKE 'theme_boost_training'
                       AND filearea  LIKE 'theme_boost_training_customimage'
                       AND filename  LIKE '__%'";
                $customicons = $DB->get_records_sql($sql);
                foreach ($customicons as $customicon) {
                    $imageurl = \moodle_url::make_file_url(
                        "{$CFG->wwwroot}/pluginfile.php",
                        implode("/", [
                            "",
                            $customicon->contextid,
                            "theme_boost_training",
                            "theme_boost_training_customimage",
                            $customicon->itemid,
                            $customicon->filename,
                        ]));
                    $formatblockcss = file_get_contents("{$CFG->dirroot}/theme/boost_training/scss/format-block.css");
                    $formatblockcss = str_replace("customiconid", $customicon->itemid, $formatblockcss);
                    $formatblockcss = str_replace("{imageurl}", $imageurl, $formatblockcss);
                    $iconscss .= $formatblockcss;
                }

                // Icons modules.
                $sql = "
                    SELECT itemid, contextid, filename
                      FROM {files}
                     WHERE component LIKE 'theme_boost_training'
                       AND filearea  LIKE 'theme_boost_training_customicon'
                       AND filename  LIKE '__%'";
                $customicons = $DB->get_records_sql($sql);
                foreach ($customicons as $customicon) {
                    $imageurl = \moodle_url::make_file_url(
                        "{$CFG->wwwroot}/pluginfile.php",
                        implode("/", [
                            "",
                            $customicon->contextid,
                            "theme_boost_training",
                            "theme_boost_training_customicon",
                            $customicon->itemid,
                            $customicon->filename,
                        ]));
                    $iconscss .= "
                        #module-{$customicon->itemid} .courseicon img,
                        .cmid-{$customicon->itemid} #page-header .activityiconcontainer img {
                            content : url('{$imageurl}');
                        }
                        #course-index-cm-{$customicon->itemid} .courseindex-link {
                            display     : flex;
                            align-items : center;
                        }
                        #course-index-cm-{$customicon->itemid} .courseindex-link::before {
                            content           : '';
                            display           : block;
                            height            : 20px;
                            width             : 20px;
                            min-width         : 20px;
                            background-image  : url('{$imageurl}');
                            background-size   : contain;
                            background-repeat : no-repeat;
                            margin-right      : 5px;
                        }
                        #course-index-cm-{$customicon->itemid}.pageitem .courseindex-link::before {
                            filter: invert(1);
                        }
                        #course-index-cm-{$customicon->itemid}.pageitem:hover .courseindex-link::before {
                            filter: invert(0);
                        }\n";
                }

                $sql = "
                    SELECT *
                      FROM {config_plugins}
                     WHERE plugin  = 'theme_boost_training'
                       AND name LIKE 'theme_boost_training_customcolor_%'";
                $customcolors = $DB->get_records_sql($sql);
                foreach ($customcolors as $customcolor) {
                    $moduleid = str_replace("theme_boost_training_customcolor_", "", $customcolor->name);
                    $iconscss .= "
                        #module-{$moduleid} .courseicon {
                            background       : {$customcolor->value} !important;
                            background-color : {$customcolor->value} !important;
                        }\n";
                }

                $iconscss = preg_replace('/\s+/s', ' ', $iconscss);
                $cache->set($cachekey, $iconscss);
            }

            $css .= $iconscss;
        }

        $cache = \cache::make("theme_boost_training", "css_cache");
        $cachekey = "background_profile_image";
        if ($cache->has($cachekey)) {
            $css .= $cache->get($cachekey);
        } else {
            $backgroundprofileurl = theme_boost_training_setting_file_url("background_profile_image");
            if ($backgroundprofileurl) {
                $profileimagecss = ":root { --background_profile: url({$backgroundprofileurl}); }";

                $cache->set($cachekey, $profileimagecss);
                $css .= $profileimagecss;
            }
        }

        echo "<style>{$css}</style>";

        if (get_config("theme_boost_training", "enable_accessibility")) {
            $PAGE->requires->js_call_amd("theme_boost_training/acctoolbar", "init");
        }

        $vlibras = get_config("theme_boost_training", "enable_vlibras") && $CFG->lang == "pt_br";
        if ($vlibras) {
            echo $OUTPUT->render_from_template("theme_boost_training/settings/vlibras", []);
        }
    }
}
