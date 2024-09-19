<?php

namespace App\Enums;

enum FileGenerationStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAIL = 'fail';
    case EXISTS = 'exists';
}
