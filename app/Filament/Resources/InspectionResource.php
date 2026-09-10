<?php

namespace App\Filament\Resources;

use App\Enums\InspectionResult;
use App\Models\Inspection;
use App\Models\Asset;
use App\Models\WorkOrder;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;

class InspectionResource extends Resource
{
    protected static ?string $model = Inspection::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Inspections';

    protected static ?string $modelLabel = 'Inspection';

    public static function getNavigationLabel(): string
    {
        return __('Inspections');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Operations');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Inspections');
    }

    public static function getModelLabel(): string
    {
        return __('Inspection');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\InspectionResource\Pages\ListInspections::route('/'),
            'create' => \App\Filament\Resources\InspectionResource\Pages\CreateInspection::route('/create'),
            'edit' => \App\Filament\Resources\InspectionResource\Pages\EditInspection::route('/{record}/edit'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Inspection Info'))
                    ->schema([
                        Forms\Components\TextInput::make('number')
                            ->label(__('Number'))
                            ->disabled()
                            ->dehydrated(false)
                            ->maxLength(30),
                        Forms\Components\Select::make('asset_id')
                            ->label(__('Asset'))
                            ->relationship('asset', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('work_order_id')
                            ->label(__('Work Order'))
                            ->relationship('workOrder', 'number')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Select::make('inspector_id')
                            ->label(__('Inspector'))
                            ->relationship('inspector', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('inspection_date')
                            ->label(__('Inspection Date'))
                            ->required(),
                        Forms\Components\Select::make('result')
                            ->label(__('Result'))
                            ->options(InspectionResult::class)
                            ->required(),
                        Forms\Components\TextInput::make('status')
                            ->label(__('Status'))
                            ->required(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make(__('Notes'))
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['asset', 'workOrder', 'inspector']))
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('number')
                    ->label(__('Number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('asset.name')
                    ->label(__('Asset'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('work_order.number')
                    ->label(__('Work Order'))
                    ->searchable(),
                TextColumn::make('inspector.name')
                    ->label(__('Inspector'))
                    ->searchable(),
                TextColumn::make('inspection_date')
                    ->label(__('Inspection Date'))
                    ->date()
                    ->sortable(),
                BadgeColumn::make('result')
                    ->label(__('Result'))
                    ->formatStateUsing(fn ($state): string => $state->getLabel())
                    ->color(fn (InspectionResult $state): string => match ($state->value) {
                        InspectionResult::Passed => 'success',
                        InspectionResult::Failed => 'danger',
                        InspectionResult::RequiresRepair => 'warning',
                        InspectionResult::RequiresFurtherTesting => 'info',
                        default => 'gray',
                    }),
                BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->formatStateUsing(fn (string $state): string => strtoupper(str_replace('_', ' ', $state)))
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'scheduled' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('result')
                    ->label(__('Result'))
                    ->options(InspectionResult::class),
                SelectFilter::make('asset_id')
                    ->label(__('Asset'))
                    ->relationship('asset', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label(__('Status')),
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
