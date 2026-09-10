<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum WorkOrderStatus: string implements HasLabel
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case AwaitingTesting = 'awaiting_testing';
    case Completed = 'completed';

    public function getLabel(): ?string
    {
        return __(Str::headline($this->value));
    }

    /** @return array<int, self> */
    public function transitions(): array
    {
        return match ($this) {
            self::Open => [self::InProgress],
            self::InProgress => [self::AwaitingTesting, self::Completed],
            self::AwaitingTesting => [self::InProgress, self::Completed],
            self::Completed => [self::InProgress], // reopen
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return in_array($to, $this->transitions(), true);
    }
}
