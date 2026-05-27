<?php

namespace App\Filament\Resources\StoreResource\Pages;

use App\Filament\Resources\StoreResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStore extends CreateRecord
{
    protected static string $resource = StoreResource::class;

    protected function afterCreate(): void
    {
        // Debug: check if area_name is being saved
        \Log::info('Store created with data:', [
            'shipping_area_id' => $this->record->shipping_area_id,
            'area_name' => $this->record->area_name,
            'form_data_area_name' => $this->data['area_name'] ?? 'not set'
        ]);
    }
}
