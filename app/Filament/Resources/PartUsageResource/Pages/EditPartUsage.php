<?php

namespace App\Filament\Resources\PartUsageResource\Pages;

use App\Filament\Resources\PartUsageResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditPartUsage extends EditRecord
{
    protected static string $resource = PartUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
