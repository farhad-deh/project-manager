<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ActiveProjectsChart extends ChartWidget
{
    protected static ?string $heading = 'پروژه‌های در حال انجام';

    protected function getData(): array
    {
        $data = Trend::model(Project::class)
            ->between(
                start: now()->subMonths(6),
                end: now(),
            )
            ->perMonth()
            ->count('id')
            ->where('status', 'doing');

        return [
            'datasets' => [
                [
                    'label' => 'پروژه‌های در حال انجام',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#F59E0B',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
