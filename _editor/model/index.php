<?php

require_once('../../../../config.php');

global $CFG;

$files = glob("*");

$items = [];
foreach ($files as $file) {
    if (file_exists("{$file}/info.json")) {
        $data = file_get_contents("{$file}/info.json");
        $data = json_decode($data);
        if ($data) {
            $items[] = [
                "id" => $file,
                "location" => $file,
                "title" => $data->title,
                "image" => "{$CFG->wwwroot}/theme/boost_training/_editor/model/{$file}/print.png",
                "preview" => "{$CFG->wwwroot}/theme/boost_training/_editor/model/{$file}/preview.html",
            ];
        }
    }
}

header("Content-Type: application/json");
echo json_encode($items);
