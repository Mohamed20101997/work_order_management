<?php

namespace App\Filament\Widgets;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Inspection;
use App\Models\Part;
use App\Models\WorkOrder;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetStatsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $statusCounts = Asset::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $total = Asset::count();

        return [
            Stat::make('Total Assets', $total)
                ->description('All registered assets')
                ->descriptionIcon('heroicon-o-archive-box')
                ->color('primary'),

            Stat::make('In Factory', $statusCounts[AssetStatus::Received->value] ?? 0)
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('info'),

            Stat::make('Under Repair', $statusCounts[AssetStatus::UnderRepair->value] ?? 0)
                ->description('Currently being repaired')
                ->descriptionIcon('heroicon-o-wrench')
                ->color('danger'),

            Stat::make('Completed', $statusCounts[AssetStatus::Completed->value] ?? 0)
                ->description('Repair completed')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Released', $statusCounts[AssetStatus::Released->value] ?? 0)
                ->description('Released to customer')
                ->descriptionIcon('heroicon-o-arrow-right-circle')
                ->color('success'),

            Stat::make('Open Work Orders', WorkOrder::query()->where('status', '!=', 'completed')->count())
                ->description('Pending work orders')
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('warning'),

            Stat::make('Pending Inspections', $statusCounts[AssetStatus::AwaitingInspection->value] ?? 0)
                ->description('Awaiting inspection')
                ->descriptionIcon('heroicon-o-magnifying-glass')
                ->color('warning'),

            Stat::make('Low Stock Parts', Part::query()->whereColumn('quantity', '<=', 'minimum_quantity')->count())
                ->description('Need reorder')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
