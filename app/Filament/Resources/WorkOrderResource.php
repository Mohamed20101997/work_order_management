<?php

namespace App\Filament\Resources;

use App\Enums\Priority;
use App\Enums\WorkOrderStatus;
use App\Models\WorkOrder;
use App\Models\Asset;
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

class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Work Orders';

    protected static ?string $modelLabel = 'Work Order';

    public static function getNavigationLabel(): string
    {
        return __('Work Orders');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Operations');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Work Orders');
    }

    public static function getModelLabel(): string
    {
        return __('Work Order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\WorkOrderResource\Pages\ListWorkOrders::route('/'),
            'create' => \App\Filament\Resources\WorkOrderResource\Pages\CreateWorkOrder::route('/create'),
            'edit' => \App\Filament\Resources\WorkOrderResource\Pages\EditWorkOrder::route('/{record}/edit'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Work Order Info'))
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
                        Forms\Components\Textarea::make('reported_problem')
                            ->label(__('Reported Problem'))
                            ->rows(3)
                            ->required(),
                        Forms\Components\Textarea::make('diagnosis')
                            ->label(__('Diagnosis'))
                            ->rows(3),
                        Forms\Components\Textarea::make('work_performed')
                            ->label(__('Work Performed'))
                            ->rows(3),
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options(WorkOrderStatus::class)
                            ->required(),
                        Forms\Components\Select::make('priority')
                            ->label(__('Priority'))
                            ->options(Priority::class)
                            ->required(),
                        Forms\Components\Select::make('assigned_to')
                            ->label(__('Assignee'))
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label(__('Due Date')),
                        Forms\Components\DateTimePicker::make('started_at')
                            ->label(__('Started At')),
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label(__('Completed At')),
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
            ->modifyQueryUsing(fn ($query) => $query->with(['asset', 'assignee']))
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
                TextColumn::make('reported_problem')
                    ->label(__('Reported Problem'))
                    ->limit(50)
                    ->description(fn (WorkOrder $record): string => $record->reported_problem),
                BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->formatStateUsing(fn ($state): string => $state->getLabel())
                    ->color(fn ($state): string => match ($state) {
                        WorkOrderStatus::Open => 'warning',
                        WorkOrderStatus::InProgress => 'info',
                        WorkOrderStatus::AwaitingTesting => 'info',
                        WorkOrderStatus::Completed => 'success',
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
                TextColumn::make('assignee.name')
                    ->label(__('Assignee'))
                    ->searchable(),
                TextColumn::make('due_date')
                    ->label(__('Due Date'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(WorkOrderStatus::class),
                SelectFilter::make('priority')
                    ->label(__('Priority'))
                    ->options(Priority::class),
                SelectFilter::make('asset_id')
                    ->label(__('Asset'))
                    ->relationship('asset', 'name')
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
