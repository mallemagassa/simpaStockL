<?php

namespace App\Filament\Clusters\StockOut\Pages;

use App\Models\Stock;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Filament\Clusters\StockOut;
use App\Filament\Exports\StockExporter;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\HasActions;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Concerns\InteractsWithTable;

class CriqueProduct extends Page implements HasTable
{
    use InteractsWithTable, HasActions;

    protected static ?string $model = Stock::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.stock-out.pages.crique-product';

    protected static ?string $cluster = StockOut::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Niveau Critique';

    protected ?string $heading = 'Produit Seuil Critique';


    public static function table(Table $table): Table
    {
        return $table
            ->query(Stock::query()->whereColumn('quantity', '<=', 'critique'))
            ->headerActions([
                ExportAction::make()
                    ->exporter(StockExporter::class)
            ])
            ->columns([
                TextColumn::make('product.name')
                ->numeric()
                ->toggleable(isToggledHiddenByDefault: false)
                ->label("Produit")
                ->sortable(),
               TextColumn::make('quantity')
                ->numeric()
                ->toggleable(isToggledHiddenByDefault: false)
                ->label("Quantite")
                ->sortable(),
               TextColumn::make('purchase_price')
                    ->money("XOF")
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label("Prix de Inventaire")
                    ->sortable(),
               TextColumn::make('sale_price')
                    ->money("XOF")
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label("Prix de Vente")
                    ->sortable(),
               TextColumn::make('critique')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
               TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label("Date de Creation")
                    ->toggleable(isToggledHiddenByDefault: true),
               TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label("Date de Modification")
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                // EditAction::make(),
                // Tables\Actions\Action::make("Entrée")
                // ->form([
                //     Section::make("Entrée")
                //         ->schema([
                //             TextInput::make('current_quantity')
                //                 ->numeric()
                //                 ->label('Quantité actuelle')
                //                 ->disabled()
                //                 ->default(fn ($record) => $record->quantity),
                //             TextInput::make('quantity')
                //                 ->numeric()
                //                 ->label('Nombre de quantités Entrées')
                //                 ->required()
                //                 ->reactive()
                //                 ->afterStateUpdated(function (Get $get, Set $set, $state) {
                //                     $currentQuantity = (float) $get('current_quantity');
                //                     $newQuantity = (float) $state;
                //                     $set('after_quantity', $currentQuantity + $newQuantity);
                //                 }),
                //             TextInput::make('after_quantity')
                //                 ->numeric()
                //                 ->label('Quantité Après Entrée')
                //                 ->disabled()
                //                 ->dehydrated()
                //                 ->default(fn ($get) => (float) $get('current_quantity')),
                //         ])
                //         ->columnSpan(6)
                // ]) ->action(function (array $data, $record) {
                //     $record->update([
                //         'quantity' => $data['after_quantity'],
                //     ]);
        
                    
                // }),
            ])
            
            ->bulkActions([
                BulkActionGroup::make([
                   DeleteBulkAction::make(),
                ]),
            ]);
    }

    // public function getTableQuery()
    // {
    //     return Stock::query()->whereColumn('quantity', '<=', 'critique');
    // }
}
