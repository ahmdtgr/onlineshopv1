<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected $listeners = ['refresh-widgets' => '$refresh'];

    protected function getStats(): array
    {
        // Get filter values from dashboard filters
        $year = $this->filters['year'] ?? Carbon::now()->year;
        $month = $this->filters['month'] ?? Carbon::now()->month;
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // Total revenue for selected month/year
        $totalRevenue = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        // Total orders for selected month/year
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Total paid orders for selected month/year
        $paidOrders = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Get month name for display (Indonesian)
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $monthName = $monthNames[$month] . ' ' . $year;

        return [
            Stat::make('Total Omset', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Periode: ' . $monthName)
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Total Order', $totalOrders)
                ->description('Periode: ' . $monthName)
                ->color('info')
                ->icon('heroicon-o-shopping-bag'),

            Stat::make('Order Terbayar', $paidOrders)
                ->description('Periode: ' . $monthName)
                ->color('warning')
                ->icon('heroicon-o-check-circle'),
        ];
    }
}
