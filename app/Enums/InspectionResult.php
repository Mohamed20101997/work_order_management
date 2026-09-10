<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum InspectionResult: string implements HasLabel
{
    case Passed = 'passed';
    case Failed = 'failed';
    case RequiresRepair = 'requires_repair';
    case RequiresFurtherTesting = 'requires_further_testing';

    public function getLabel(): ?string
    {
        return __(Str::headline($this->value));
    }
}
