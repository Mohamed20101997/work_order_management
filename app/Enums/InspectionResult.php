<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum InspectionResult: string
{
    case Passed = 'passed';
    case Failed = 'failed';
    case RequiresRepair = 'requires_repair';
    case RequiresFurtherTesting = 'requires_further_testing';

    public function label(): string
    {
        return Str::headline($this->value);
    }
}
