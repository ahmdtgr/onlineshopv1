<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';

    protected static ?string $title = 'Ulasan Produk';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable(),
                Tables\Columns\TextColumn::make('orderItem.product_name')
                    ->label('Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('orderItem.variant_name')
                    ->label('Varian')
                    ->getStateUsing(function ($record) {
                        $item = $record->orderItem;
                        if (!$item)
                            return '-';

                        $parts = [];
                        if ($item->variant_type1 && $item->variant_option1) {
                            $parts[] = $item->variant_option1;
                        }
                        if ($item->variant_type2 && $item->variant_option2) {
                            $parts[] = $item->variant_option2;
                        }

                        return !empty($parts) ? implode(' / ', $parts) : '-';
                    }),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn($state) => str_repeat('⭐', $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('review')
                    ->label('Ulasan')
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn($record) => $record->review),
                Tables\Columns\ImageColumn::make('images')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->stacked()
                    ->limit(3),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Rating')
                    ->options([
                        '5' => '⭐⭐⭐⭐⭐ (5)',
                        '4' => '⭐⭐⭐⭐ (4)',
                        '3' => '⭐⭐⭐ (3)',
                        '2' => '⭐⭐ (2)',
                        '1' => '⭐ (1)',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->form([
                        Forms\Components\TextInput::make('user.name')
                            ->label('Pengguna')
                            ->formatStateUsing(fn($record) => $record->user?->name ?? '-'),
                        Forms\Components\TextInput::make('orderItem.product_name')
                            ->label('Produk')
                            ->formatStateUsing(fn($record) => $record->orderItem?->product_name ?? '-'),
                        Forms\Components\TextInput::make('rating')
                            ->label('Rating')
                            ->formatStateUsing(fn($state) => str_repeat('⭐', $state) . " ({$state}/5)"),
                        Forms\Components\Textarea::make('review')
                            ->label('Ulasan')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('images')
                            ->label('Foto Review')
                            ->multiple()
                            ->disk('public')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada ulasan')
            ->emptyStateDescription('Pelanggan belum memberikan ulasan untuk pesanan ini.')
            ->emptyStateIcon('heroicon-o-star');
    }
}
