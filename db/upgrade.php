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
 * @package   theme_boost_training
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * function xmldb_supervideo_upgrade
 *
 * @param int $oldversion
 *
 * @return bool
 * @throws Exception
 */
function xmldb_theme_boost_training_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2025062800) {

        // Define table theme_boost_training_pages to be created.
        $table = new xmldb_table("theme_boost_training_pages");

        // Adding fields to table theme_boost_training_pages.
        $table->add_field("id", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field("local", XMLDB_TYPE_CHAR, "30", null, XMLDB_NOTNULL);
        $table->add_field("type", XMLDB_TYPE_CHAR, "30", null, XMLDB_NOTNULL);
        $table->add_field("title", XMLDB_TYPE_CHAR, "255", null, XMLDB_NOTNULL);
        $table->add_field("html", XMLDB_TYPE_TEXT);
        $table->add_field("info", XMLDB_TYPE_TEXT);
        $table->add_field("lang", XMLDB_TYPE_CHAR, "6");
        $table->add_field("sort", XMLDB_TYPE_INTEGER, "10");

        // Adding keys to table theme_boost_training_pages.
        $table->add_key("primary", XMLDB_KEY_PRIMARY, ["id"]);

        // Conditionally launch create table for theme_boost_training_pages.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Boost Training savepoint reached.
        upgrade_plugin_savepoint(true, 2025062800, "theme", "boost_training");
    }

    return true;
}
