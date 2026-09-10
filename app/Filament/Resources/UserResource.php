<?php

namespace App\Filament\Resources;

use App\Models\User;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $modelLabel = 'User';

    public static function getNavigationLabel(): string
    {
        return __('Users');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Users');
    }

    public static function getModelLabel(): string
    {
        return __('User');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\UserResource\Pages\ListUsers::route('/'),
            'create' => \App\Filament\Resources\UserResource\Pages\CreateUser::route('/create'),
            'edit' => \App\Filament\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('User Info'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label(__('Password'))
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->maxLength(255)
                            ->placeholder(__('Leave blank to keep current password'))
                            ->default(fn (?string $operation): string => $operation === 'create' ? 'password' : '')
                            ->afterStateHydrated(static function (Forms\Components\TextInput $component, $state): void {
                                if (filled($state)) {
                                    $component->state('');
                                }
                            })
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->statePath('password'),
                        Forms\Components\Select::make('role')
                            ->label(__('Role'))
                            ->options([
                                'admin' => __('Admin'),
                                'manager' => __('Manager'),
                                'technician' => __('Technician'),
                                'viewer' => __('Viewer'),
                            ])
                            ->required(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make(__('Status'))
                    ->schema([
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
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable(),
                TextColumn::make('role')
                    ->label(__('Role'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'manager' => 'warning',
                        'technician' => 'info',
                        'viewer' => 'gray',
                        default => 'gray',
                    }),
                ToggleColumn::make('is_active')
                    ->label(__('Active')),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label(__('Role'))
                    ->options([
                        'admin' => 'Admin',
                        'manager' => 'Manager',
                        'technician' => 'Technician',
                        'viewer' => 'Viewer',
                    ]),
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
