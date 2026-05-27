<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BestSellingProductTable extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 3;
    protected static ?string $heading = 'Produk Terlaris';

    protected $listeners = ['refresh-widgets' => '$refresh'];
    
    public function table(Table $table): Table
    {
        // Get filter values from dashboard filters
        $year = $this->filters['year'] ?? Carbon::now()->year;
        $month = $this->filters['month'] ?? Carbon::now()->month;
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        return $table
            ->query(
                OrderItem::query()
                    ->select([
                        'product_id',
                        'product_name',
                        DB::raw('COUNT(DISTINCT order_id) as total_orders'),
                        DB::raw('SUM(quantity) as total_quantity'),
                        DB::raw('SUM(price * quantity) as total_revenue')
                    ])
                    ->whereHas('order', function($query) use ($startDate, $endDate) {
                        $query->where('payment_status', 'paid')
                              ->whereBetween('created_at', [$startDate, $endDate]);
                    })
                    ->groupBy('product_id', 'product_name')
                    ->orderByDesc('total_quantity')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Nama Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Total Terjual')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Total Pendapatan')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->paginated(false);
    }

    public function getTableRecordKey(Model $record): string
    {
        return (string) $record->product_id;
    }

    public function getTableHeading(): string
    {
        $year = $this->filters['year'] ?? Carbon::now()->year;
        $month = $this->filters['month'] ?? Carbon::now()->month;
        
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $monthName = $monthNames[$month] . ' ' . $year;

        return 'Produk Terlaris - ' . $monthName;
    }
}