<?php

namespace App\Filament\Resources\PartUsageResource\Pages;

use App\Filament\Resources\PartUsageResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListPartUsages extends ListRecords
{
    protected static string $resource = PartUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
