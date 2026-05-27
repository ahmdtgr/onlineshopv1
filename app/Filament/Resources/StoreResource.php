<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Models\Store;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Services\BiteshipService;
use Filament\Forms\Components\Wizard;
use Filament\Notifications\Notification;

class StoreResource extends Resource
{
   protected static ?string $model = Store::class;
   protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
   protected static ?string $navigationGroup = 'Settings';
   protected static ?int $navigationSort = 2;

   public static function form(Form $form): Form
   {
       return $form
           ->schema([
               Wizard::make([
                   Wizard\Step::make('Basic Information')
                       ->icon('heroicon-o-information-circle')
                       ->schema([
                            Forms\Components\TextInput::make('name')
                               ->required()
                               ->maxLength(255),
                            Forms\Components\Textarea::make('description')
                               ->columnSpanFull(),
                            Forms\Components\TextInput::make('whatsapp')
                               ->prefix('62')
                               ->helperText('Mohon masukan nomor tanpa angka 0 diawal. Contoh 812345678900')
                               ->placeholder('812345678900')
                               ->required()
                               ->numeric()
                               ->dehydrateStateUsing(fn ($state) => '62' . ltrim($state, '62'))
                               ->formatStateUsing(fn ($state) => ltrim($state, '62'))
                               ->validationAttribute('Nomor WhatsApp')
                               ->maxLength(255),
                            Forms\Components\FileUpload::make('image')
                               ->image()
                               ->directory('stores'),
                            Forms\Components\FileUpload::make('banner')
                               ->directory('stores/banner'),
                         
                            Forms\Components\Toggle::make('is_use_payment_gateway')
                               ->label('Use Payment Gateway'),
                            Forms\Components\ColorPicker::make('primary_color'),
                            Forms\Components\ColorPicker::make('secondary_color'),
                       ]),
                   Wizard\Step::make('Shipping Configuration')
                       ->icon('heroicon-o-truck')
                       ->schema([
                           Forms\Components\Select::make('shipping_provider')
                               ->label('Provider Pengiriman')
                               ->options(['biteship' => 'biteship'])
                               ->default('biteship')
                               ->required()
                               ->live(),
   
                           Forms\Components\Textarea::make('shipping_api_key')
                               ->label('API Key')
                               ->required()
                               ->live(),
   
                            Forms\Components\Select::make('area_name')
                               ->label('Area Pengiriman')
                               ->searchable()
                               ->helperText('Ketikan nama kota/kecamatan untuk memilih lokasi')
                               ->getSearchResultsUsing(function (string $search, Get $get) {
                                   $apiKey = $get('shipping_api_key');
                                   if (!$apiKey) return [];
                           
                                   $biteshipService = new BiteshipService($apiKey);
                                   $areas = $biteshipService->searchAreas($search);
                                   
                                   return collect($areas)->mapWithKeys(fn ($area) => [$area['name'] => $area['name']])->toArray();
                               })
                               ->required()
                               ->live()
                               ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                   if ($state) {
                                       $apiKey = $get('shipping_api_key');
                                       if ($apiKey) {
                                           $biteshipService = new BiteshipService($apiKey);
                                           $areas = $biteshipService->searchAreas($state);
                                           
                                           foreach ($areas as $area) {
                                               if ($area['name'] == $state) {
                                                   $set('shipping_area_id', $area['id']);
                                                   \Log::info('Area selected - Name: ' . $state . ', ID: ' . $area['id']);
                                                   break;
                                               }
                                           }
                                       }
                                   }
                               })
                               ->disabled(fn (Get $get) => !$get('shipping_api_key')),

                            Forms\Components\Hidden::make('shipping_area_id'),
                            Forms\Components\Select::make('shipping_courier')
                               ->multiple()
                               ->required()
                               ->options(function (Get $get) {
                                    $apiKey = $get('shipping_api_key');
                                    if (!$apiKey) return [];
                                    try {
                                        $biteshipService = new BiteshipService($get('shipping_api_key'));
                                        $couriers = $biteshipService->getCouriers();
                                        
                                        return collect($couriers)
                                            ->mapWithKeys(function ($courier) {
                                                return [
                                                    $courier['courier_code'] => 
                                                        "{$courier['courier_name']} - {$courier['courier_service_name']} ({$courier['description']})"
                                                ];
                                            })
                                            ->toArray();
                                    } catch (\Exception $e) {
                                        return [
                                            'jne' => 'JNE Regular',
                                            'sicepat' => 'SiCepat Regular',
                                            'jnt' => 'J&T Regular',
                                            'pos' => 'POS Indonesia'
                                        ];
                                    }
                               })
                               ->searchable()
                               ->preload()
                               ->columnSpanFull()
                               ->default(['jne', 'sicepat', 'jnt'])
                               ->dehydrateStateUsing(fn (array $state) => implode(',', $state))
                               ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                                   if (is_string($state)) {
                                       $component->state(explode(':', $state));
                                   }
                               })
                               ->helperText('Pilih kurir pengiriman yang akan digunakan')
                               ->loadingMessage('Loading couriers...')
                               ->noSearchResultsMessage('No couriers found.')
                               ->searchingMessage('Searching...')
                               ->searchDebounce(500),
                            
                           Forms\Components\Textarea::make('address')
                               ->required()
                               ->maxLength(255)
                               ->disabled(fn (Get $get) => !$get('shipping_api_key')),
                       ])
               ])->columnSpanFull()
           ]);
   }
   
   public static function table(Table $table): Table
   {
       return $table
           ->columns([
               Tables\Columns\TextColumn::make('name')
                   ->searchable(),
               Tables\Columns\ImageColumn::make('image')->circular(),
               Tables\Columns\TextColumn::make('whatsapp')
                   ->searchable(),
               Tables\Columns\ToggleColumn::make('is_use_payment_gateway')
                   ->label('Payment Gateway'),
               Tables\Columns\TextColumn::make('created_at')
                   ->dateTime()
                   ->sortable()
                   ->toggleable(isToggledHiddenByDefault: true),
               Tables\Columns\TextColumn::make('updated_at')
                   ->dateTime()
                   ->sortable()
                   ->toggleable(isToggledHiddenByDefault: true),
           ])
           ->filters([])
           ->actions([
               Tables\Actions\EditAction::make(),
           ])
           ->bulkActions([
               Tables\Actions\BulkActionGroup::make([
                   Tables\Actions\DeleteBulkAction::make(),
               ]),
           ]);
   }

   public static function getRelations(): array
   {
       return [];
   }

   public static function getPages(): array
   {
       return [
           'index' => Pages\ListStores::route('/'),
           'create' => Pages\CreateStore::route('/create'),
           'edit' => Pages\EditStore::route('/{record}/edit'),
       ];
   }

   public static function canCreate(): bool
   {
       return Store::count() < 1;
   }
}