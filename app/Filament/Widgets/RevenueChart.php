<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Grafik Omset Harian';
    protected static ?int $sort = 2;

    protected $listeners = ['refresh-widgets' => '$refresh'];

    protected function getData(): array
    {
        // Get filter values from dashboard filters
        $year = $this->filters['year'] ?? Carbon::now()->year;
        $month = $this->filters['month'] ?? Carbon::now()->month;
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $data = Order::query()
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Omset (Rp)',
                    'data' => $data->pluck('total')->toArray(),
                    'fill' => false,
                    'borderColor' => '#4CAF50',
                    'backgroundColor' => 'rgba(76, 175, 80, 0.1)',
                    'tension' => 0.1,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ]
            ],
            'labels' => $data->pluck('date')
                ->map(function($date) {
                    return Carbon::parse($date)->format('d M');
                })
                ->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getHeading(): string
    {
        $year = $this->filters['year'] ?? Carbon::now()->year;
        $month = $this->filters['month'] ?? Carbon::now()->month;
        
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $monthName = $monthNames[$month] . ' ' . $year;

        return 'Grafik Omset Harian - ' . $monthName;
    }
}