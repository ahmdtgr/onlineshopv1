<?php

namespace App\Filament\Resources\StoreResource\Pages;

use App\Filament\Resources\StoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStore extends EditRecord
{
    protected static string $resource = StoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Debug: check if area_name is being saved
        \Log::info('Store saved with data:', [
            'shipping_area_id' => $this->record->shipping_area_id,
            'area_name' => $this->record->area_name,
            'form_data_area_name' => $this->data['area_name'] ?? 'not set'
        ]);
    }
}
