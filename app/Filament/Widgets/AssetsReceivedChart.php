<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class AssetsReceivedChart extends ChartWidget
{
    public function getHeading(): \Illuminate\Contracts\Support\Htmlable | string | null
    {
        return __('Assets Received (Last 12 Months)');
    }
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i));
        }

        $labels = $months->map(fn ($m) => $m->translatedFormat('M Y'))->toArray();

        $counts = Cache::remember(
            'dashboard:assets-received-12m',
            now()->addMinutes(5),
            fn (): array => $months->map(fn ($m) => Asset::query()
                ->whereYear('received_date', $m->year)
                ->whereMonth('received_date', $m->month)
                ->count()
            )->values()->toArray(),
        );

        return [
            'datasets' => [
                [
                    'label' => 'Assets Received',
                    'data' => $counts,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => '#3b82f6',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#3b82f6',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
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
                    'ticks' => [
                        'maxRotation' => 45,
                    ],
                ],
            ],
        ];
    }
}
