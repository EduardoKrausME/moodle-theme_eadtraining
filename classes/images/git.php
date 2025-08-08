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
 * Git Class
 *
 * @package   theme_eadtraining
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @copyright based on work by 2016 Frédéric Massart - FMCorz.net
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_eadtraining\images;

use Exception;

/**
 * Class git
 */
class git {
    /**
     * list_all
     *
     * @param $prefix
     * @param $filenamechecked
     * @return array
     * @throws Exception
     */
    public static function list_all($prefix, $filenamechecked) {
        // URL to the raw JSON file hosted on GitHub.
        $url = "https://raw.githubusercontent.com/EduardoKrausME/moodle-theme_eadtraining--images/master/images.json";

        // Load the JSON content from the URL.
        $json = file_get_contents($url);

        // Check if content was loaded successfully.
        if ($json === false) {
            throw new Exception("Error: Unable to load JSON from the URL.");
        }

        // Decode the JSON into an associative array.
        $data = json_decode($json, true);

        // Check if decoding was successful.
        if (!is_array($data)) {
            throw new Exception("Error: Invalid JSON structure.");
        }

        $banners = [];
        foreach ($data as $folder) {
            if (strpos($folder, $prefix) === 0) {
                $folders = explode("/", $folder);
                $banners[$folders[1]]["title"] = get_string("quickstart_banner-{$folders[1]}", "theme_eadtraining");

                $filename = pathinfo($folders[2], PATHINFO_FILENAME);
                $imageurl = "https://raw.githubusercontent.com/EduardoKrausME/moodle-theme_eadtraining--images/master/{$folder}";
                $banners[$folders[1]]["files"][] = [
                    "image" => $imageurl,
                    "title" => get_string("quickstart_banner-{$filename}", "theme_eadtraining"),
                    "filename" => $filename,
                    "checked" => $filenamechecked == $filename,
                ];
            }
        }

        return array_values($banners);
    }
}
