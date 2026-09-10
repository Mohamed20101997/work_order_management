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

    public static function getNavigationLabel(): string
    {
        return __('Assets');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Assets');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Assets');
    }

    public static function getModelLabel(): string
    {
        return __('Asset');
    }

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
                Forms\Components\Section::make(__('Asset Info'))
                    ->schema([
                        Forms\Components\Select::make('asset_type_id')
                            ->label(__('Asset Type'))
                            ->relationship('assetType', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('Name'))
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('code')
                                    ->label(__('Code'))
                                    ->maxLength(50),
                            ])
                            ->required(),
                        Forms\Components\Select::make('company_id')
                            ->label(__('Company'))
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('Name'))
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('asset_number')
                            ->label(__('Asset Number'))
                            ->disabled()
                            ->dehydrated(false)
                            ->maxLength(30),
                        Forms\Components\TextInput::make('serial_number')
                            ->label(__('Serial Number'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('manufacturer')
                            ->label(__('Manufacturer'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('model')
                            ->label(__('Model'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('location')
                            ->label(__('Location'))
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('received_date')
                            ->label(__('Received Date'))
                            ->required(),
                        Forms\Components\DatePicker::make('expected_release_date')
                            ->label(__('Expected Release Date')),
                        Forms\Components\Select::make('priority')
                            ->label(__('Priority'))
                            ->options(Priority::class)
                            ->required(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make(__('Description'))
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(3),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(3),
                        SpatieMediaLibraryFileUpload::make('attachments')
                            ->label(__('Attachments'))
                            ->collection('attachments')
                            ->multiple()
                            ->image()
                            ->imageEditor()
                            ->maxSize(8192)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->directory('asset-attachments'),
                    ]),
                Forms\Components\Section::make(__('Status'))
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options(AssetStatus::class)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['company', 'assetType']))
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('asset_number')
                    ->label(__('Asset Number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('Company'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assetType.name')
                    ->label(__('Asset Type'))
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->formatStateUsing(fn ($state): string => $state->getLabel())
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
                    ->label(__('Priority'))
                    ->formatStateUsing(fn ($state): string => $state->getLabel())
                    ->color(fn ($state): string => match ($state) {
                        Priority::Low => 'success',
                        Priority::Normal => 'info',
                        Priority::High => 'warning',
                        Priority::Urgent => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('received_date')
                    ->label(__('Received Date'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(AssetStatus::class),
                SelectFilter::make('company_id')
                    ->label(__('Company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('asset_type_id')
                    ->label(__('Asset Type'))
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
