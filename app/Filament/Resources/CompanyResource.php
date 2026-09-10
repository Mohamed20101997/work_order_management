<?php

namespace App\Filament\Resources;

use App\Models\Company;
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

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationLabel = 'Companies';

    protected static ?string $modelLabel = 'Company';

    public static function getNavigationLabel(): string
    {
        return __('Companies');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Companies');
    }

    public static function getModelLabel(): string
    {
        return __('Company');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\CompanyResource\Pages\ListCompanies::route('/'),
            'create' => \App\Filament\Resources\CompanyResource\Pages\CreateCompany::route('/create'),
            'edit' => \App\Filament\Resources\CompanyResource\Pages\EditCompany::route('/{record}/edit'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Company Info'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('contact_person')
                            ->label(__('Contact Person'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('Phone'))
                            ->maxLength(50),
                        Forms\Components\TextInput::make('email')
                            ->label(__('Email'))
                            ->email(),
                        Forms\Components\TextInput::make('address')
                            ->label(__('Address'))
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Forms\Components\Section::make(__('Notes'))
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(3),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Active')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contact_person')
                    ->label(__('Contact Person'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('Phone')),
                TextColumn::make('email')
                    ->label(__('Email')),
                TextColumn::make('assets_count')
                    ->label(__('Asset Count'))
                    ->counts('assets')
                    ->numeric(),
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
