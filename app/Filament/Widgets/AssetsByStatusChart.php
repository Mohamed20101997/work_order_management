<?php

namespace App\Filament\Widgets;

use App\Enums\AssetStatus;
use App\Models\Asset;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class AssetsByStatusChart extends ChartWidget
{
    public function getHeading(): \Illuminate\Contracts\Support\Htmlable | string | null
    {
        return __('Assets by Status');
    }
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $statusCounts = Cache::remember(
            'dashboard:asset-status-counts',
            now()->addMinute(),
            fn (): array => Asset::query()
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
        );

        $labels = [];
        $data = [];
        $colors = [];

        $statusConfig = [
            AssetStatus::Received->value => ['label' => 'Received', 'color' => '#3b82f6'],
            AssetStatus::AwaitingInspection->value => ['label' => 'Awaiting Inspection', 'color' => '#f59e0b'],
            AssetStatus::UnderRepair->value => ['label' => 'Under Repair', 'color' => '#ef4444'],
            AssetStatus::AwaitingTesting->value => ['label' => 'Awaiting Testing', 'color' => '#8b5cf6'],
            AssetStatus::Completed->value => ['label' => 'Completed', 'color' => '#22c55e'],
            AssetStatus::Released->value => ['label' => 'Released', 'color' => '#6b7280'],
        ];

        foreach ($statusConfig as $key => $config) {
            if (($statusCounts[$key] ?? 0) > 0) {
                $labels[] = __($config['label']);
                $data[] = $statusCounts[$key];
                $colors[] = $config['color'];
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                    'hoverBorderWidth' => 2,
                    'hoverBorderColor' => '#ffffff',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 16,
                        'usePointStyle' => true,
                        'pointStyleWidth' => 10,
                    ],
                ],
            ],
            'cutout' => '65%',
        ];
    }
}
