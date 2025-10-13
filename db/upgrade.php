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
 * Upgrade file
 *
 * @package   theme_eadtraining
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_eadtraining\admin\setting_scss;

/**
 * function xmldb_supervideo_upgrade
 *
 * @param int $oldversion
 * @return bool
 * @throws Exception
 */
function xmldb_theme_eadtraining_upgrade($oldversion) {
    global $DB;

    if ($oldversion < 2025090300) {
        set_config("top_scroll_fix", 1, "theme_eadtraining");
        set_config("top_scroll_background_color", "", "theme_eadtraining");

        upgrade_plugin_savepoint(true, 2025090300, "theme", "eadtraining");
    }

    if ($oldversion < 2025090500) {
        $itens = ["differentials", "featured", "pricing"];
        foreach ($itens as $item) {
            $DB->execute("UPDATE {theme_eadtraining_pages} SET template = '{$item}' WHERE template LIKE '{$item}-%'");
        }

        upgrade_plugin_savepoint(true, 2025090500, "theme", "eadtraining");
    }

    if ($oldversion < 2025100400) {
        $settingscss = new setting_scss("test", "test", "", "");
        $scss = get_config("theme_eadtraining", "scss");

        if ($settingscss->validate($scss) === true) {
            set_config("scsspos", $scss, "theme_eadtraining");
        }

        upgrade_plugin_savepoint(true, 2025100400, "theme", "eadtraining");
    }

    if ($oldversion < 2025101300) {
        $settingscss = new setting_scss("test", "test", "", "");

        $scss = get_config("theme_eadtraining", "scsspos");
        if (!$settingscss->validate($scss) === true) {
            set_config("scsspos", "", "theme_eadtraining");
        }

        $scss = get_config("theme_eadtraining", "scsspre");
        if (!$settingscss->validate($scss) === true) {
            set_config("scsspre", "", "theme_eadtraining");
        }

        upgrade_plugin_savepoint(true, 2025101300, "theme", "eadtraining");
    }



    return true;
}
