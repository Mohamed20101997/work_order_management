<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentActivityWidget extends TableWidget
{
    public function getHeading(): \Illuminate\Contracts\Support\Htmlable | string | null
    {
        return __('Recent Activity');
    }
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::with('user')
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label(__('Activity'))
                    ->searchable()
                    ->limit(80),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('User'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Time'))
                    ->dateTime()
                    ->sortable()
                    ->description(fn (Activity $record): string => $record->created_at->diffForHumans()),
            ])
            ->paginated([8]);
    }
}
