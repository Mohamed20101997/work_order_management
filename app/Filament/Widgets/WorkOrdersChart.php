<?php

namespace App\Filament\Widgets;

use App\Models\WorkOrder;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class WorkOrdersChart extends ChartWidget
{
    public function getHeading(): \Illuminate\Contracts\Support\Htmlable | string | null
    {
        return __('Work Orders by Status');
    }
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $statusCounts = Cache::remember(
            'dashboard:work-order-status-counts',
            now()->addMinute(),
            fn (): array => WorkOrder::query()
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
        );

        $labels = array_map('__', ['Open', 'In Progress', 'Awaiting Testing', 'Completed']);
        $data = [
            $statusCounts['open'] ?? 0,
            $statusCounts['in_progress'] ?? 0,
            $statusCounts['awaiting_testing'] ?? 0,
            $statusCounts['completed'] ?? 0,
        ];
        $colors = ['#f59e0b', '#3b82f6', '#8b5cf6', '#22c55e'];

        return [
            'datasets' => [
                [
                    'label' => 'Work Orders',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderRadius' => 8,
                    'borderSkipped' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                    'grid' => [
                        'color' => '#f1f5f9',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }
}
