<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VariantTypeResource\Pages;
use App\Filament\Resources\VariantTypeResource\RelationManagers;
use App\Models\VariantType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VariantTypeResource extends Resource
{
    protected static ?string $model = VariantType::class;

    protected static ?string $navigationIcon = 'heroicon-s-adjustments-horizontal';
    protected static ?string $navigationGroup = 'Products';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationLabel = 'Variant Types';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Contoh: Ukuran, Warna, Rasa, dll.')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nama'),
                Tables\Columns\TextColumn::make('variant_options_count')
                    ->counts('variantOptions')
                    ->label('Jumlah Opsi'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\VariantOptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVariantTypes::route('/'),
            'create' => Pages\CreateVariantType::route('/create'),
            'edit' => Pages\EditVariantType::route('/{record}/edit'),
        ];
    }
}