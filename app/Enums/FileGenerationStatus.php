<?php

namespace App\Enums;

enum FileGenerationStatus: string
{
    case SUCCESS = 'success';
    case FAIL = 'fail';
    case EXISTS = 'exists';
}
