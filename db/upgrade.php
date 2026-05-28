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
        if ($settingscss->validate($scss) !== true) {
            set_config("scsspos", "", "theme_eadtraining");
        }

        $scss = get_config("theme_eadtraining", "scsspre");
        if ($settingscss->validate($scss) !== true) {
            set_config("scsspre", "", "theme_eadtraining");
        }

        upgrade_plugin_savepoint(true, 2025101300, "theme", "eadtraining");
    }

    if ($oldversion < 2026022500) {
        set_config("secondary", "#ced4da", "theme_boost");
        set_config("navbarlayout", "classic", "theme_eadtraining");

        upgrade_plugin_savepoint(true, 2026022500, "theme", "eadtraining");
    }

    if ($oldversion < 2026022800) {
        $records = $DB->get_records_select(
            'config_plugins',
            "plugin = 'theme_eadtraining' AND name LIKE 'override_course_color_%'"
        );

        if ($records) {
            foreach ($records as $record) {
                $suffix = substr($record->name, strlen("override_course_color_"));
                $newname = "override_course_primarycolor_{$suffix}";

                set_config($newname, $record->value, 'theme_eadtraining');
                unset_config($record->name, 'theme_eadtraining');
            }
        }
        upgrade_plugin_savepoint(true, 2026022800, "theme", "eadtraining");
    }

    if ($oldversion < 2026030700) {
        set_config("breadcrumb_show_mycourses_courses", 0, "theme_eadtraining");
        set_config("breadcrumb_show_categories", 0, "theme_eadtraining");
        set_config("breadcrumb_show_course", 0, "theme_eadtraining");
        set_config("breadcrumb_show_navigation_duplicates", 0, "theme_eadtraining");
        set_config("breadcrumb_show_sections", 0, "theme_eadtraining");
        set_config("breadcrumb_show_no_link_items", 0, "theme_eadtraining");

        upgrade_plugin_savepoint(true, 2026030700, "theme", "eadtraining");
    }

    if ($oldversion < 2026052800) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('theme_eadtraining_accesslog');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('action', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);
            $table->add_field('item', XMLDB_TYPE_CHAR, '100', null, null, null, null);
            $table->add_field('status', XMLDB_TYPE_CHAR, '20', null, null, null, null);
            $table->add_field('activeitems', XMLDB_TYPE_TEXT, null, null, null, null, null);
            $table->add_field('statejson', XMLDB_TYPE_TEXT, null, null, null, null, null);
            $table->add_field('pageurl', XMLDB_TYPE_TEXT, null, null, null, null, null);
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0');

            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, ['userid']);
            $table->add_index('action', XMLDB_INDEX_NOTUNIQUE, ['action']);
            $table->add_index('timecreated', XMLDB_INDEX_NOTUNIQUE, ['timecreated']);

            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2026052800, 'theme', 'eadtraining');
    }

    return true;
}
