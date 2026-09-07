<?php

namespace App\Filament\Resources;

use App\Models\PartUsage;
use App\Models\Part;
use App\Models\WorkOrder;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;

class PartUsageResource extends Resource
{
    protected static ?string $model = PartUsage::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Part Usages';

    protected static ?string $modelLabel = 'Part Usage';

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PartUsageResource\Pages\ListPartUsages::route('/'),
            'create' => \App\Filament\Resources\PartUsageResource\Pages\CreatePartUsage::route('/create'),
            'edit' => \App\Filament\Resources\PartUsageResource\Pages\EditPartUsage::route('/{record}/edit'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Part Usage Info')
                    ->schema([
                        Forms\Components\Select::make('part_id')
                            ->label('Part')
                            ->relationship('part', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('work_order_id')
                            ->label('Work Order')
                            ->relationship('workOrder', 'number')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->required(),
                        Forms\Components\DatePicker::make('used_at')
                            ->label('Used At')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('part.name')
                    ->label('Part')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('work_order.number')
                    ->label('Work Order')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('used_at')
                    ->label('Used At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('part_id')
                    ->label('Part')
                    ->relationship('part', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('work_order_id')
                    ->label('Work Order')
                    ->relationship('workOrder', 'number')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }
}
