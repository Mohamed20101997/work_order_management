<?php

namespace App\Filament\Widgets;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Inspection;
use App\Models\Part;
use App\Models\WorkOrder;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class AssetStatsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $counts = Cache::remember('dashboard:asset-stats', now()->addMinute(), function (): array {
            return [
                'status' => Asset::query()
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
                'total' => Asset::count(),
                'open_work_orders' => WorkOrder::query()->where('status', '!=', 'completed')->count(),
                'low_stock_parts' => Part::query()->whereColumn('quantity', '<=', 'minimum_quantity')->count(),
            ];
        });

        $statusCounts = $counts['status'];
        $total = $counts['total'];

        return [
            Stat::make(__('Total Assets'), $total)
                ->description(__('All registered assets'))
                ->descriptionIcon('heroicon-o-archive-box')
                ->color('primary'),

            Stat::make(__('In Factory'), $statusCounts[AssetStatus::Received->value] ?? 0)
                ->description(__('Awaiting processing'))
                ->descriptionIcon('heroicon-o-building-office')
                ->color('info'),

            Stat::make(__('Under Repair'), $statusCounts[AssetStatus::UnderRepair->value] ?? 0)
                ->description(__('Currently being repaired'))
                ->descriptionIcon('heroicon-o-wrench')
                ->color('danger'),

            Stat::make(__('Completed'), $statusCounts[AssetStatus::Completed->value] ?? 0)
                ->description(__('Repair completed'))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(__('Released'), $statusCounts[AssetStatus::Released->value] ?? 0)
                ->description(__('Released to customer'))
                ->descriptionIcon('heroicon-o-arrow-right-circle')
                ->color('success'),

            Stat::make(__('Open Work Orders'), $counts['open_work_orders'])
                ->description(__('Pending work orders'))
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('warning'),

            Stat::make(__('Pending Inspections'), $statusCounts[AssetStatus::AwaitingInspection->value] ?? 0)
                ->description(__('Awaiting inspection'))
                ->descriptionIcon('heroicon-o-magnifying-glass')
                ->color('warning'),

            Stat::make(__('Low Stock Parts'), $counts['low_stock_parts'])
                ->description(__('Need reorder'))
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
