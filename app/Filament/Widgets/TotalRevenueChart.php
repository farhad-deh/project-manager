<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\ProjectPayment;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class TotalRevenueChart extends ChartWidget
{
    protected static ?string $heading = 'مجموع دریافتی‌ها';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $data = Trend::model(ProjectPayment::class)
            ->between(
                start: now()->subMonths(6),
                end: now(),
            )
            ->dateColumn('paid_at') // مهم: استفاده از تاریخ درست
            ->perMonth()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'دریافتی (IRR)',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => Carbon::parse($value->date)->format('Y-m')
            ),];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
