<?php

namespace App\Services;

class FileDataGenerator
{
    public function createFileContentWithSize($sizeInBytes)
    {
        $character = '0';
        $content = str_repeat($character, $sizeInBytes);

        return $content;
    }
}
