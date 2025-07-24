<?php

use core_course\external\course_summary_exporter;
use core_external\util;

function banner_eadflix_op2_createblocks($page) {
    global $DB, $OUTPUT, $CFG;

    $accesscourse = get_string("access_course", "theme_boost_training");
    $page->info = json_decode($page->info);

    $blocks = "";
    if (isset($page->info->savedata)) {
        foreach ($page->info->savedata as $data) {
            $course = $DB->get_record("course", ["id" => $data->courseid]);
            if ($course) {
                $course = new core_course_list_element($course);

                $courseimage = course_summary_exporter::get_course_image($course);
                if (!$courseimage) {
                    $courseimage = $OUTPUT->get_default_image_for_courseid($course->id);
                }

                $blocks .= "
                    <div class=\"course-banner-item\">
                        <div class=\"course-bg-banner\">
                            <div class=\"course-bg-images video-bg-player\"
                                 style=\"background-image:url('{$courseimage}')\"></div>
                            <div class=\"course-bg-overlay\"></div>
                        </div>
                        <div class=\"course-banner-content\">
                            <h3 class=\"course-title\">
                                <a href=\"{$CFG->wwwroot}/course/view.php?id={$course->id}\">{$course->fullname}</a>
                            </h3>
                            <div class=\"course-text-description\">
                                {$data->description}
                            </div>
                            <a class=\"btn btn-access\" href=\"{$CFG->wwwroot}/course/view.php?id={$course->id}\">{$accesscourse}</a>
                        </div>
                    </div>\n";
            }
        }
    }

    return "<div class=\"owl-carousel owl-course-banner\">{$blocks}</div>";
}
