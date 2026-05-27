<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ProductVariant;
use App\Models\VariantType;
use App\Models\VariantOption;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return (bool) $ownerRecord->has_variants;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informasi Varian')
                            ->schema([
                                // Variant Type 1
                                Forms\Components\Select::make('variant_type1')
                                    ->label('Jenis Varian 1')
                                    ->options(VariantType::pluck('name', 'name')->toArray())
                                    ->searchable()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(VariantType::class, 'name'),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        return VariantType::create($data)->name;
                                    })
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('variant_option1', null)),
                                
                                // Variant Option 1
                                Forms\Components\Select::make('variant_option1')
                                    ->label('Opsi Varian 1')
                                    ->options(function (Get $get) {
                                        $typeName = $get('variant_type1');
                                        if (!$typeName) return [];
                                        
                                        $variantType = VariantType::where('name', $typeName)->first();
                                        if (!$variantType) return [];
                                        
                                        return VariantOption::where('variant_type_id', $variantType->id)
                                            ->pluck('name', 'name')
                                            ->toArray();
                                    })
                                    ->searchable()
                                    ->required()
                                    ->createOptionForm(function (Get $get) {
                                        // Get the variant type ID directly
                                        $typeName = $get('variant_type1');
                                        $variantType = VariantType::where('name', $typeName)->first();
                                        $variantTypeId = $variantType ? $variantType->id : null;

                                        return [
                                            Forms\Components\Hidden::make('variant_type_id')
                                                ->default($variantTypeId),
                                            Forms\Components\TextInput::make('name')
                                                ->required()
                                                ->maxLength(255)
                                        ];
                                    })
                                    ->createOptionUsing(function (array $data) {
                                        $option = VariantOption::create($data);
                                        return $option->name;
                                    }),
                                
                                // Variant Type 2
                                Forms\Components\Select::make('variant_type2')
                                    ->label('Jenis Varian 2')
                                    ->options(function (Get $get) {
                                        $selectedType = $get('variant_type1');
                                        return VariantType::when($selectedType, 
                                            fn($query) => $query->where('name', '!=', $selectedType)
                                        )->pluck('name', 'name')->toArray();
                                    })
                                    ->searchable()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(VariantType::class, 'name'),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        return VariantType::create($data)->name;
                                    })
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('variant_option2', null))
                                    ->helperText('Biarkan kosong jika hanya ada 1 jenis varian'),
                                
                                // Variant Option 2
                                Forms\Components\Select::make('variant_option2')
                                    ->label('Opsi Varian 2')
                                    ->options(function (Get $get) {
                                        $typeName = $get('variant_type2');
                                        if (!$typeName) return [];
                                        
                                        $variantType = VariantType::where('name', $typeName)->first();
                                        if (!$variantType) return [];
                                        
                                        return VariantOption::where('variant_type_id', $variantType->id)
                                            ->pluck('name', 'name')
                                            ->toArray();
                                    })
                                    ->searchable()
                                    ->createOptionForm(function (Get $get) {
                                        // Get the variant type ID directly
                                        $typeName = $get('variant_type2');
                                        $variantType = VariantType::where('name', $typeName)->first();
                                        $variantTypeId = $variantType ? $variantType->id : null;

                                        return [
                                            Forms\Components\Hidden::make('variant_type_id')
                                                ->default($variantTypeId),
                                            Forms\Components\TextInput::make('name')
                                                ->required()
                                                ->maxLength(255)
                                        ];
                                    })
                                    ->createOptionUsing(function (array $data) {
                                        $option = VariantOption::create($data);
                                        return $option->name;
                                    })
                                    ->hidden(fn (Get $get): bool => empty($get('variant_type2')))
                                    ->helperText('Biarkan kosong jika hanya ada 1 jenis varian'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),
                
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Harga & Stok')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp'),
                                    
                                Forms\Components\TextInput::make('stock')
                                    ->required()
                                    ->numeric(),
                                    
                                Forms\Components\Toggle::make('is_active')
                                    ->required()
                                    ->default(true)
                                    ->label('Active'),
                            ]),
                            
                        Forms\Components\Section::make('SKU')
                            ->schema([
                                Forms\Components\TextInput::make('sku')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(100)
                                    ->helperText('Biarkan kosong untuk dibuat otomatis'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('variant_name')
            ->columns([
                Tables\Columns\TextColumn::make('variant_name')
                    ->label('Varian'),
                Tables\Columns\TextColumn::make('sku')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('variant_type1')
                    ->label('Jenis Varian 1')
                    ->options(VariantType::pluck('name', 'name')),
                Tables\Filters\SelectFilter::make('variant_type2')
                    ->label('Jenis Varian 2')
                    ->options(VariantType::pluck('name', 'name')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}