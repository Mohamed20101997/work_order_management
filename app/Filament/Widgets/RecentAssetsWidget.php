<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentAssetsWidget extends TableWidget
{
    protected static ?string $heading = 'Recent Assets';
    protected static ?int $sort = 4;
    protected static ?int $pollInterval = 30;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Asset::with(['company', 'assetType'])
                    ->latest('received_date')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('asset_number')
                    ->label('Asset #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable(),

                Tables\Columns\TextColumn::make('assetType.name')
                    ->label('Type')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        \App\Enums\AssetStatus::Received => 'info',
                        \App\Enums\AssetStatus::AwaitingInspection => 'warning',
                        \App\Enums\AssetStatus::UnderRepair => 'danger',
                        \App\Enums\AssetStatus::AwaitingTesting => 'info',
                        \App\Enums\AssetStatus::Completed => 'success',
                        \App\Enums\AssetStatus::Released => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state): string => $state->label()),

                Tables\Columns\TextColumn::make('received_date')
                    ->label('Received')
                    ->date()
                    ->sortable(),
            ])
            ->paginated([5]);
    }
}
