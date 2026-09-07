<?php

namespace App\Filament\Widgets;

use App\Models\WorkOrder;
use Filament\Widgets\ChartWidget;

class WorkOrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Work Orders by Status';
    protected static ?int $sort = 3;
    protected static ?int $pollInterval = 30;

    protected function getData(): array
    {
        $statusCounts = WorkOrder::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = ['Open', 'In Progress', 'Awaiting Testing', 'Completed'];
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
