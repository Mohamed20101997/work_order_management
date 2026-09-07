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
                Forms\Components\Section::make('Inspection Info')
                    ->schema([
                        Forms\Components\TextInput::make('number')
                            ->label('Number')
                            ->disabled()
                            ->dehydrated(false)
                            ->maxLength(30),
                        Forms\Components\Select::make('asset_id')
                            ->label('Asset')
                            ->relationship('asset', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('work_order_id')
                            ->label('Work Order')
                            ->relationship('workOrder', 'number')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Select::make('inspector_id')
                            ->label('Inspector')
                            ->relationship('inspector', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('inspection_date')
                            ->label('Inspection Date')
                            ->required(),
                        Forms\Components\Select::make('result')
                            ->label('Result')
                            ->options(InspectionResult::class)
                            ->required(),
                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->required(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('asset.name')
                    ->label('Asset')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('work_order.number')
                    ->label('Work Order')
                    ->searchable(),
                TextColumn::make('inspector.name')
                    ->label('Inspector')
                    ->searchable(),
                TextColumn::make('inspection_date')
                    ->label('Inspection Date')
                    ->date()
                    ->sortable(),
                BadgeColumn::make('result')
                    ->label('Result')
                    ->color(fn (InspectionResult $state): string => match ($state->value) {
                        InspectionResult::Passed => 'success',
                        InspectionResult::Failed => 'danger',
                        InspectionResult::RequiresRepair => 'warning',
                        InspectionResult::RequiresFurtherTesting => 'info',
                        default => 'gray',
                    }),
                BadgeColumn::make('status')
                    ->label('Status')
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
                    ->label('Result')
                    ->options(InspectionResult::class),
                SelectFilter::make('asset_id')
                    ->label('Asset')
                    ->relationship('asset', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Status'),
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
