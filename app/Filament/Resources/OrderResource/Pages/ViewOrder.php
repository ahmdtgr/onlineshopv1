<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use App\Services\OrderStatusService;
use Filament\Tables\Actions\Action;
use App\Models\Order;
use Filament\Notifications\Notification;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve_order')
                ->label('Proses Pesanan')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => 
                    $this->record->payment_status === 'paid' && 
                    !$this->record->is_approved
                )
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Proses Pesanan')
                ->modalDescription('Apakah Anda yakin ingin memproses pesanan ini? Setelah diproses, pesanan akan masuk ke tahap persiapan.')
                ->action(function () {
                    $this->record->update([
                        'is_approved' => true,
                    ]);
                    
                    Notification::make()
                        ->title('Berhasil!')
                        ->body('Order disetujui dan masuk ke tahap persiapan.')
                        ->success()
                        ->send();
                }),
            
            // Action::make('download_invoice')
            //     ->label('Invoice')
            //     ->icon('heroicon-o-document-arrow-down')
            //     ->color('success')
            //     ->url(fn (Order $record) => url("invoice/{$record->id}/download"))
            //     ->openUrlInNewTab(),
            
            
            Actions\EditAction::make(),
        ];
    }
}