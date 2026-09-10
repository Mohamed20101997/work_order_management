<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum AssetStatus: string implements HasLabel
{
    case Received = 'received';
    case AwaitingInspection = 'awaiting_inspection';
    case UnderRepair = 'under_repair';
    case AwaitingTesting = 'awaiting_testing';
    case Completed = 'completed';
    case Released = 'released';

    public function getLabel(): ?string
    {
        return __(Str::headline($this->value));
    }

    /** @return array<int, self> */
    public function transitions(): array
    {
        return match ($this) {
            self::Received => [self::AwaitingInspection, self::UnderRepair],
            self::AwaitingInspection => [self::UnderRepair, self::AwaitingTesting, self::Completed],
            self::UnderRepair => [self::AwaitingTesting, self::Completed],
            self::AwaitingTesting => [self::UnderRepair, self::Completed],
            self::Completed => [self::Released, self::UnderRepair],
            self::Released => [],
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return in_array($to, $this->transitions(), true);
    }
}
