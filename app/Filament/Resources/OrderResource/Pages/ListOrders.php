<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Exports\OrderItemsDetailExport;
use App\Exports\OrdersSummaryExport;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ActionGroup::make([
                Actions\Action::make('export_summary')
                    ->label('Export Summary')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn() => $this->exportSummary()),
                Actions\Action::make('export_detail')
                    ->label('Export Detail')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(fn() => $this->exportDetail()),
            ])->label('Export Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning')
                ->button(),

        ];
    }

    public function exportSummary(): ?BinaryFileResponse
    {
        $orderIds = $this->getOrderIdsFromCurrentFilter();

        if (blank($orderIds)) {
            Notification::make()
                ->title('Tidak ada data')
                ->body('Tidak ada pesanan untuk diexport berdasarkan filter saat ini.')
                ->warning()
                ->send();

            return null;
        }

        return Excel::download(
            new OrdersSummaryExport($orderIds),
            'orders-summary-' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function exportDetail(): ?BinaryFileResponse
    {
        $orderIds = $this->getOrderIdsFromCurrentFilter();

        if (blank($orderIds)) {
            Notification::make()
                ->title('Tidak ada data')
                ->body('Tidak ada pesanan untuk diexport berdasarkan filter saat ini.')
                ->warning()
                ->send();

            return null;
        }

        return Excel::download(
            new OrderItemsDetailExport($orderIds),
            'orders-detail-' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    private function getOrderIdsFromCurrentFilter(): array
    {
        return (clone $this->getFilteredTableQuery())
            ->select('orders.id')
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    public function getTabs(): array
    {
        return [
            'unpaid' => Tab::make('Belum Bayar')
                ->icon('heroicon-o-credit-card')
                ->badge(fn(): int => Order::where('status', 'pending')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(
                    fn(Builder $query): Builder =>
                    $query->where('status', 'pending')
                ),

            'perlu_diproses' => Tab::make('Menunggu Konfirmasi')
                ->icon('heroicon-o-exclamation-triangle')
                ->badge(fn(): int => Order::where('status', 'awaiting_confirmation')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(
                    fn(Builder $query): Builder =>
                    $query->where('status', 'awaiting_confirmation')
                ),

            'preparing' => Tab::make('Diproses')
                ->icon('heroicon-o-cube')
                ->badge(fn(): int => Order::where('status', 'processing')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(
                    fn(Builder $query): Builder =>
                    $query->where('status', 'processing')
                ),

            'transit' => Tab::make('Dalam Pengiriman')
                ->icon('heroicon-o-truck')
                ->badge(fn(): int => Order::where('status', 'shipped')->count())
                ->badgeColor('primary')
                ->modifyQueryUsing(
                    fn(Builder $query): Builder =>
                    $query->where('status', 'shipped')
                ),

            'delivered' => Tab::make('Terkirim')
                ->icon('heroicon-o-check-circle')
                ->badge(fn(): int => Order::where('status', 'completed')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(
                    fn(Builder $query): Builder =>
                    $query->where('status', 'completed')
                ),

            'cancelled' => Tab::make('Dibatalkan')
                ->icon('heroicon-o-x-circle')
                ->badge(fn(): int => Order::where('status', 'cancelled')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(
                    fn(Builder $query): Builder =>
                    $query->where('status', 'cancelled')
                ),
        ];
    }
}
