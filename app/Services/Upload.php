<?php

namespace App\Services;

class Upload
{
    public static function uploadFile($file)
    {
        if (isset($file)) {

            $uploadPath = "" . PUBLICO . "";

            $targetFile = $uploadPath . basename($file["name"]);

            if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                return true;
            }
        }
    }
    public static function uploadStories($file)
    {
        if (isset($file)) {

            $uploadPath = "" . STORIES . "";

            $targetFile = $uploadPath . basename($file["name"]);

            if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                return true;
            }
        }
    }
    public static function uploadFileCat($file)
    {
        if (isset($file)) {

            $uploadPath = "" . PUBLICOSITE . "";

            $targetFile = $uploadPath . basename($file["name"]);

            if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                return true;
            }
        }
    }
}