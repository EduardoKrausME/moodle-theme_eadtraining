<?php

use core_course\external\course_summary_exporter;
use core_external\util;

function popular_eadflix_createblocks($page) {
    global $DB, $OUTPUT, $CFG;

    $page->info = json_decode($page->info);

    $blocks = "";
    if (isset($page->info->savedata->courseid)) {
        foreach ($page->info->savedata->courseid as $courseid) {
            $course = $DB->get_record("course", array("id" => $courseid));
            if ($course) {
                $course = new core_course_list_element($course);

                $courseimage = course_summary_exporter::get_course_image($course);
                if (!$courseimage) {
                    $coursecontext = context_course::instance($course->id, IGNORE_MISSING);
                    $courseimage = $OUTPUT->get_generated_url_for_course($coursecontext);
                }

                $context = context_course::instance($course->id, IGNORE_MISSING);
                list(
                    $course->summary, $course->summaryformat
                    ) = util::format_text($course->summary, $course->summaryformat, $context, "course", "summary", 0);

                $blocks .= "
                    <div class=\"top-courses-item slider-item overflow-hidden\">
                        <div class=\"top-courses-inner\">
                            <a href=\"{$CFG->wwwroot}/course/view.php?id={$course->id}\"
                               style=\"
                                    background:          url('{$courseimage}');
                                    display:             block;
                                    width:               200px;
                                    height:              320px;
                                    background-size:     cover;
                                    background-position: center;\">
                            </a>
                            <div class=\"content-back\">
                                <h6 class=\"course-title\">
                                    <a href=\"{$CFG->wwwroot}/course/view.php?id={$course->id}\">Título</a>
                                </h6>
                                <div class=\"video-description\">{$course->summary}</div>
                                <a href=\"{$CFG->wwwroot}/course/view.php?id={$course->id}\">
                                    <span>" . get_string("access_course", "theme_boost_training") . "</span>
                                </a>
                            </div>
                        </div>
                    </div>\n";
            }
        }
    }

    return "<div class=\"owl-courses-content owl-carousel\">{$blocks}</div>";
}
