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

    public static function getNavigationLabel(): string
    {
        return __('Part Usages');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Operations');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Part Usages');
    }

    public static function getModelLabel(): string
    {
        return __('Part Usage');
    }

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
                Forms\Components\Section::make(__('Part Usage Info'))
                    ->schema([
                        Forms\Components\Select::make('part_id')
                            ->label(__('Part'))
                            ->relationship('part', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('work_order_id')
                            ->label(__('Work Order'))
                            ->relationship('workOrder', 'number')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->label(__('User'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->label(__('Quantity'))
                            ->numeric()
                            ->required(),
                        Forms\Components\DatePicker::make('used_at')
                            ->label(__('Used At'))
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['part', 'workOrder', 'user']))
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('part.name')
                    ->label(__('Part'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('work_order.number')
                    ->label(__('Work Order'))
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(__('User'))
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label(__('Quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('used_at')
                    ->label(__('Used At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('part_id')
                    ->label(__('Part'))
                    ->relationship('part', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('work_order_id')
                    ->label(__('Work Order'))
                    ->relationship('workOrder', 'number')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('user_id')
                    ->label(__('User'))
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
