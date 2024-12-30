<?php

namespace App\Filament\Clusters\StockOut\Resources\StockResource\Pages;

use Closure;
use App\Models\Out;
use App\Models\Shop;
use App\Models\Stock;
use Filament\Actions;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Clusters\StockOut\Resources\StockResource;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use App\Filament\Clusters\StockOut\Resources\StockResource\Widgets\StockOverview;
use App\Filament\Clusters\StockOut\Resources\StockResource\Widgets\AmountTotalInv;
use App\Filament\Clusters\StockOut\Resources\StockResource\Widgets\AmountTotalSale;

class ListStocks extends ListRecords
{
    protected static string $resource = StockResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StockOverview::class   
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make("Sortie")
            ->modalWidth('7xl')
            ->form([
                Section::make(heading: "Info")
                    ->schema(components: [
                       TextInput::make('ref')
                        ->required()
                        ->label('Ref')
                        ->maxLength(255)
                        ->default(function () {
                            $out = Out::latest('id')->first();
                    
                            $lastNumber = $out ? (int) substr($out->ref, 4) : 0;
                            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                    
                            return "BS{$newNumber}";
                        })
                        ->disabled()
                        ->dehydrated(),
                        Select::make(name: 'shop_id')
                            ->label(label: 'Boutique')
                            ->options(options: Shop::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        DatePicker::make(name: 'date_out')
                            ->required()
                            ->label(label: 'Date de sortie'),
                    ])
                    ->columns(2)
                    ->columnSpan(6)
                    ->collapsible()
                    ->collapsed(fn($record) => $record ? true : false),
                Section::make("Articles")
                    ->schema([
                        Repeater::make('items')
                            ->hiddenLabel()
                            ->collapsible()
                            ->collapsed(fn($record) => $record ? true : false)
                            ->cloneable()
                            ->label('Article')
                            ->itemLabel('Article')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Article') 
                                    ->options(
                                        Product::whereIn('id', Stock::pluck('product_id'))->get()->mapWithKeys(function ($product) {
                                            return [
                                                $product->id => $product->name . ' (' . $product->code . ')'
                                            ];
                                        })->toArray()
                                    )
                                    ->required()
                                    ->columnSpan(3)
                                    ->searchable(),
                                MoneyInput::make('price_sale')
                                    ->required()
                                    ->disabled()
                                    ->columnSpan(2)
                                    ->label('Prix Vente'),
                                    //->numeric(),
                                TextInput::make('quantity')
                                    ->live(debounce: 500)
                                    ->required()
                                    ->columnSpan(1)
                                    ->label('Quantité')
                                    ->default(1)
                                    ->rules([
                                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            $stock = Stock::where("product_id", $get('product_id'))->first();
                                            // dd($get('quantity'), $attribute, $value, $get('product_id'), $stock);
                                            if ($get('quantity') > $stock->quantity) {
                                                $fail("La quantité saisie est inférieure à la quantité disponible en stock : ".$stock->quantity);
                                            }
                                        },
                                    ])
                                    ->numeric(),
                                Grid::make(12)
                                    ->schema([
                                        Toggle::make('enable_price')
                                            ->label('Activer NV prix Vente')
                                            ->onColor('success')
                                            ->offColor('danger')
                                            ->reactive()
                                            ->columnSpan(6),
                                        MoneyInput::make('price')
                                            ->label('Prix personnalisé')
                                            //->required()
                                            ->live(debounce:500)
                                            ->disabled(fn (Get $get) => !$get('enable_price'))
                                            //->numeric()
                                            ->rules([
                                                fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                                    if ($get('enable_price') && empty($value)) {
                                                        $fail("Le prix personnalisé est requis lorsque 'Activer NV prix Vente' est activé.");
                                                    }
                                                },
                                            ])
                                            ->columnSpan(6),
                                    ])
                                    ->label('Prix personnalisé')
                                    ->columnSpan(4),
                                
                                MoneyInput::make('total')
                                    ->label('Montant')
                                    ->columnSpan(2)
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(0)
                                    //->numeric()
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('total', str_replace([' ', ' '], '', $state)))
                                    ,
                            ])
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $items = $get('items');
                                $totalSale = 0;
                                $totalPurchase = 0;
                            
                                foreach ($items as $key => $item) {
                                    if (isset($item['product_id'])) {
                                        $product = Stock::where("product_id", $item['product_id'])->first();
                                        $salePrice = (float) $product->sale_price;
                                        $purchasePrice = (float) $product->purchase_price;
                                        $quantity = (int) $item['quantity'];
                            
                                        // Utiliser le prix personnalisé si activé et défini
                                        $effectivePrice = ($item['enable_price'] ?? false) && !empty($item['price']) 
                                            ? (float)  str_replace([' ', ' '], '', $item['price'])
                                            : $salePrice;
                            
                                        // Calcul du total pour cet article
                                        $items[$key]['price_sale'] = number_format($salePrice, 0, ',', ' '); // Prix par défaut
                                        $items[$key]['total'] =  number_format($quantity * $effectivePrice, 0, ',', ' ');
                            
                                        // Totaux globaux
                                        $totalSale += str_replace([' ', ' '], '', $items[$key]['total']);
                                        $totalPurchase +=  str_replace([' ', ' '], '', $purchasePrice * $quantity);
                                    }
                                }
                            
                                $profit = $totalSale - $totalPurchase;
                            
                                // Mettre à jour les états
                                $set('items', $items);
                                $set('amount_total_sale', number_format($totalSale, 0, ',', ' '));
                                $set('amount_total_purchase', number_format($totalPurchase, 0, ',', ' '));
                                $set('profit', number_format($profit, 0, ',', ' '));
                            })
                            ->lazy()
                            ->columns(12)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpan(6)
                    ->collapsible()
                    ->collapsed(fn($record) => $record ? true : false),

                // Section pour les observations
                Section::make("Observation")
                    ->schema([
                        TextInput::make('observation')->label('Observation'),
                    ])
                    ->columnSpan(6)
                    ->collapsible()
                    ->collapsed(fn($record) => $record ? true : false),

                // Section pour les totaux
                Section::make("Totaux")
                    ->schema([
                        MoneyInput::make('amount_total_sale')
                        ->label('Total Vente')
                        // ->numeric()
                        ->disabled()
                        ->dehydrated()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('amount_total_sale', str_replace([' ', ' '], '', $state)))
                        ->reactive(),
                        MoneyInput::make('amount_total_purchase')
                        ->label('Total Achat')
                        // ->numeric()
                        ->disabled()
                        ->dehydrated()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('amount_total_purchase', str_replace([' ', ' '], '', $state))),
                        MoneyInput::make('profit')
                        ->label('Bénéfice')
                        //->numeric()
                        ->disabled()
                        ->dehydrated()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('profit', str_replace([' ', ' '], '', $state))),
                    ])
                    ->columnSpan(6)
                    ->collapsible()
                    ->collapsed(fn($record) => $record ? true : false),
            ])
            ->action(function (array $data, ?Out $record): void {
                //dd($data);
                // Crée une nouvelle sortie
                $record = $record ?? new Out();
                $record->fill($data);
                $record->save();
        
                foreach ($data['items'] as $itemData) {
                    $stockG = Stock::where('product_id',$itemData['product_id'])->first();
                    
        
                    $stockG->quantity -= $itemData['quantity'];
                    $stockG->save();

                    if ( $stockG->quantity <= $stockG->critique ) {
                        $recipient = auth()->user();

                        $recipient->notify(Notification::make()
                            ->title('Baise de Quantite de Produit')
                            ->danger()
                            ->body($stockG->product->name.'( Code du Produit:  '.$stockG->product->code.' ) ')
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('Voir')
                                    ->button()
                                    ->url(route('filament.admin.stock-out.resources.stocks.view', ['record' => $stockG->id]))
                                    ->markAsRead()
                            ])
                            ->toDatabase());

                            
 
                        // $recipient->notify(
                        //     Notification::make()
                        //         ->title('Saved successfully')
                        //         ->toDatabase(),
                        // );

                        Notification::make()
                        ->title('L\'opération a été effectuée avec succès.')
                        ->success()
                        ->send();
                    }
        
                   
                    $record->outItems()->create($itemData);
                }
            })
            ->color('info'),
        ];
    }
}
