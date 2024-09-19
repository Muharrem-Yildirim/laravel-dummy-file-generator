<?php

function format_bytes($bytes, $decimals = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$decimals}f %s", $bytes / pow(1024, $factor), $units[$factor]);
}
