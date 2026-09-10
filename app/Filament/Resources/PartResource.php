<?php

namespace App\Filament\Resources;

use App\Models\Part;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;

class PartResource extends Resource
{
    protected static ?string $model = Part::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Parts';

    protected static ?string $modelLabel = 'Part';

    public static function getNavigationLabel(): string
    {
        return __('Parts');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Operations');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Parts');
    }

    public static function getModelLabel(): string
    {
        return __('Part');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PartResource\Pages\ListParts::route('/'),
            'create' => \App\Filament\Resources\PartResource\Pages\CreatePart::route('/create'),
            'edit' => \App\Filament\Resources\PartResource\Pages\EditPart::route('/{record}/edit'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Part Info'))
                    ->schema([
                        Forms\Components\TextInput::make('part_number')
                            ->label(__('Part Number'))
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(3),
                        Forms\Components\TextInput::make('quantity')
                            ->label(__('Quantity'))
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('minimum_quantity')
                            ->label(__('Minimum Quantity'))
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('unit')
                            ->label(__('Unit'))
                            ->maxLength(20),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('part_number')
                    ->label(__('Part Number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('Description'))
                    ->limit(50),
                TextColumn::make('quantity')
                    ->label(__('Quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('minimum_quantity')
                    ->label(__('Minimum Quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit')
                    ->label(__('Unit')),
                ToggleColumn::make('is_active')
                    ->label(__('Active')),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label(__('Active'))
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ]),
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
