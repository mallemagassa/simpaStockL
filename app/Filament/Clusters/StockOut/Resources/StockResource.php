<?php

namespace App\Filament\Clusters\StockOut\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Stock;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use App\Filament\Clusters\StockOut;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use App\Filament\Exports\StockExporter;
use App\Filament\Imports\StockImporter;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use App\Filament\Clusters\StockOut\Resources\StockResource\Pages;
use App\Filament\Clusters\StockOut\Resources\StockResource\RelationManagers;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-m-square-3-stack-3d';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = StockOut::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(name: 'product_id')
                ->label(label: 'Produit')
                ->options(options: Product::all()->pluck('name', 'id'))
                ->required()
                ->label("Produit")
                ->searchable(),
            Forms\Components\TextInput::make('quantity')
                ->required()
                ->label("Quantite")
                ->numeric(),
                MoneyInput::make('purchase_price')
                ->required()
                ->label("Prix de Inventaire")
                ->decimals(0)
                //->suffix("F CFA")
                // ->format('fr_FR')
                ->afterStateUpdated(fn ($state, callable $set) => $set('purchase_price', str_replace([' ', ' '], '', $state))),
                //->integer(),
                MoneyInput::make('sale_price')
                    ->label("Prix de Vente")
                    ->required()
                    ->decimals(0)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('sale_price', str_replace([' ', ' '], '', $state))),
                    //->numeric(),
                Forms\Components\TextInput::make('critique')
                    ->numeric()
                    ->label("Niveau Critique")
                    ->default(null),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(StockExporter::class),
                ImportAction::make()
                    ->importer(StockImporter::class)
            ])
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                ->numeric()
                ->toggleable(isToggledHiddenByDefault: false)
                ->label("Produit")
                ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                ->numeric()
                ->toggleable(isToggledHiddenByDefault: false)
                ->label("Quantite")
                ->sortable(),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->money("XOF")
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label("Prix de Inventaire")
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->money("XOF")
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label("Prix de Vente")
                    ->sortable(),
                Tables\Columns\TextColumn::make('critique')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label("Date de Creation")
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label("Date de Modification")
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                DateFilter::make('created_at')->label("Date de Creation")
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make("Entrée")
                    ->form([
                        Section::make("Entrée")
                            ->schema([
                                TextInput::make('current_quantity')
                                    ->numeric()
                                    ->label('Quantité actuelle')
                                    ->disabled()
                                    ->default(fn ($record) => $record->quantity),
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->label('Nombre de quantités Entrées')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $currentQuantity = (float) $get('current_quantity');
                                        $newQuantity = (float) $state;
                                        $set('after_quantity', $currentQuantity + $newQuantity);
                                    }),
                                TextInput::make('after_quantity')
                                    ->numeric()
                                    ->label('Quantité Après Entrée')
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(fn ($get) => (float) $get('current_quantity')),
                            ])
                            ->columnSpan(6)
                    ]) 
                    ->icon("heroicon-s-arrow-right-start-on-rectangle")
                    ->action(function (array $data, $record) {
                        $record->update([
                            'quantity' => $data['after_quantity'],
                        ]);
            
                        
                    }),
                    
                ]),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'view' => Pages\ViewStock::route('/{record}'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
            // 'critique' =>  Pages\CriqueProduct::route('/'),
        ];
    }
}
