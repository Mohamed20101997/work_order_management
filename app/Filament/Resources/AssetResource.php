<?php

namespace App\Filament\Resources;

use App\Enums\AssetStatus;
use App\Enums\Priority;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Assets';

    protected static ?string $navigationLabel = 'Assets';

    protected static ?string $modelLabel = 'Asset';

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\AssetResource\Pages\ListAssets::route('/'),
            'create' => \App\Filament\Resources\AssetResource\Pages\CreateAsset::route('/create'),
            'view' => \App\Filament\Resources\AssetResource\Pages\ViewAsset::route('/{record}'),
            'edit' => \App\Filament\Resources\AssetResource\Pages\EditAsset::route('/{record}/edit'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Asset Info')
                    ->schema([
                        Forms\Components\Select::make('asset_type_id')
                            ->label('Asset Type')
                            ->relationship('assetType', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('company_id')
                            ->label('Company')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('asset_number')
                            ->label('Asset Number')
                            ->disabled()
                            ->dehydrated(false)
                            ->maxLength(30),
                        Forms\Components\TextInput::make('serial_number')
                            ->label('Serial Number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('manufacturer')
                            ->label('Manufacturer')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('model')
                            ->label('Model')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('location')
                            ->label('Location')
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('received_date')
                            ->label('Received Date')
                            ->required(),
                        Forms\Components\DatePicker::make('expected_release_date')
                            ->label('Expected Release Date'),
                        Forms\Components\Select::make('priority')
                            ->label('Priority')
                            ->options(Priority::class)
                            ->required(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3),
                        SpatieMediaLibraryFileUpload::make('attachments')
                            ->label('Attachments')
                            ->collection('attachments')
                            ->multiple()
                            ->preserveFilenames()
                            ->disk('public'),
                    ]),
                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(AssetStatus::class)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset_number')
                    ->label('Asset Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assetType.name')
                    ->label('Asset Type')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state): string => strtoupper(str_replace('_', ' ', $state->value)))
                    ->color(fn ($state): string => match ($state) {
                        AssetStatus::Received => 'info',
                        AssetStatus::AwaitingInspection => 'warning',
                        AssetStatus::UnderRepair => 'danger',
                        AssetStatus::AwaitingTesting => 'info',
                        AssetStatus::Completed => 'success',
                        AssetStatus::Released => 'success',
                        default => 'gray',
                    }),
                BadgeColumn::make('priority')
                    ->label('Priority')
                    ->formatStateUsing(fn ($state): string => strtoupper($state->value))
                    ->color(fn ($state): string => match ($state) {
                        Priority::Low => 'success',
                        Priority::Normal => 'info',
                        Priority::High => 'warning',
                        Priority::Urgent => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('received_date')
                    ->label('Received Date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(AssetStatus::class),
                SelectFilter::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('asset_type_id')
                    ->label('Asset Type')
                    ->relationship('assetType', 'name')
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
