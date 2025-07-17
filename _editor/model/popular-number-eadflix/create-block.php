<?php

use core_course\external\course_summary_exporter;

function popular_number_EadFlix_createblocks($page) {
    global $DB, $PAGE, $CFG;

    $page->info = json_decode($page->info);

    $num = 0;
    $blocks = "";
    if (isset($page->info->savedata->courseid)) {
        foreach ($page->info->savedata->courseid as $courseid) {
            $course = $DB->get_record("course", array("id" => $courseid));
            if ($course) {
                $course = new core_course_list_element($course);

                $courseimage = course_summary_exporter::get_course_image($course);
                if (!$courseimage) {
                    $coursecontext = context_course::instance($course->id, IGNORE_MISSING);
                    $courseimage = $PAGE->output->get_generated_url_for_course($coursecontext);
                }

                $num++;
                $blocks .= "
                   <div class=\"top-courses-item slider-item\">
                       <div class=\"top-courses-inner top-courses-number\">
                           <div class=\"EadFlix-nunber\">{$num}</div>
                           <a href=\"{$CFG->wwwroot}/course/view.php?id={$course->id}\"
                              style=\"
                                    background:          url('{$courseimage}');
                                    display:             block;
                                    width:               200px;
                                    height:              320px;
                                    background-size:     cover;
                                    background-position: center;\">
                           </a>
                       </div>
                   </div>\n";
            }
        }
    }

    return "<div class=\"owl-courses-content owl-carousel\">{$blocks}</div>";
}
