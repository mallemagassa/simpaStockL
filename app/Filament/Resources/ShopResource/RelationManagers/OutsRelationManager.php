<?php

namespace App\Filament\Resources\ShopResource\RelationManagers;

use Closure;
use App\Models\Out;
use Filament\Forms;
use App\Models\Shop;
use Filament\Tables;
use App\Models\Stock;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;

class OutsRelationManager extends RelationManager
{
    protected static string $relationship = 'outs';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
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
            ->collapsible(),
            // ->collapsed(fn($record) => $record ? true : false),
        Section::make("Articles")
            ->schema([
                Repeater::make('items')
                    ->hiddenLabel()
                    ->collapsible()
                    // ->collapsed(fn($record) => $record ? true : false)
                    ->relationship('outItems')
                    ->mutateRelationshipDataBeforeSaveUsing(function (Model $record, array $data): array {
                        $stock = Stock::where('product_id', $record->product_id)->first();
                        if ($stock) {
                            $stock->quantity += $record->quantity - $data['quantity'];
                            $stock->save();
                        }
                       
                        return $data;
                    })
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
                            ->label('Prix Vente')
                            ->afterStateUpdated(fn ($state, callable $set) => $set('price_sale', str_replace([' ', ' '], '', $state))),
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
                                    // ->numeric()
                                    ->rules([
                                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            if ($get('enable_price') && empty($value)) {
                                                $fail("Le prix personnalisé est requis lorsque 'Activer NV prix Vente' est activé.");
                                            }
                                        },
                                    ])
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('price', str_replace([' ', ' '], '', $state)))
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
                            ->afterStateUpdated(fn ($state, callable $set) => $set('total', str_replace([' ', ' '], '', $state)))
                            // ->numeric(),
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
            ->collapsible(),
            // ->collapsed(fn($record) => $record ? true : false),

        // Section pour les observations
        Section::make("Observation")
            ->schema([
                TextInput::make('observation')->label('Observation'),
            ])
            ->columnSpan(6)
            ->collapsible(),
            // ->collapsed(fn($record) => $record ? true : false),

        // Section pour les totaux
        Section::make("Totaux")
            ->schema([
                MoneyInput::make('amount_total_sale')
                ->label('Total Vente')
                // ->numeric()
                ->afterStateUpdated(fn ($state, callable $set) => $set('amount_total_sale', str_replace([' ', ' '], '', $state)))
                ->disabled()
                ->dehydrated()
                ->reactive(),
                MoneyInput::make('amount_total_purchase')
                ->label('Total Achat')
                // ->numeric()
                ->disabled()
                ->afterStateUpdated(fn ($state, callable $set) => $set('amount_total_purchase', str_replace([' ', ' '], '', $state)))
                ->dehydrated()
                ->reactive(),
                MoneyInput::make('profit')
                ->label('Bénéfice')
                // ->numeric()
                ->disabled()
                ->dehydrated()
                ->afterStateUpdated(fn ($state, callable $set) => $set('profit', str_replace([' ', ' '], '', $state)))
                ->reactive(),
            ])
            ->columnSpan(6)
            ->collapsible(),
            // ->collapsed(fn($record) => $record ? true : false),
    
            // Forms\Components\TextInput::make('profit')
            //     ->required()
            //     ->numeric(),
            // Forms\Components\TextInput::make('amount_total_sale')
            //     ->required()
            //     ->numeric(),
            // Forms\Components\TextInput::make('amount_total_purchase')
            //     ->required()
            //     ->numeric(),
            // Forms\Components\TextInput::make('ref')
            //     ->required()
            //     ->maxLength(30),
            // Forms\Components\TextInput::make('observation')
            //     ->maxLength(30)
            //     ->default(null),
            // Forms\Components\TextInput::make('shop_id')
            //     ->required()
            //     ->numeric(),
        ]);
    }

    public function table(Table $table): Table
    {
        
        return $table
            ->recordTitleAttribute('Sortie')
            ->columns([
                Tables\Columns\TextColumn::make('ref')
                ->label("Ref")
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
                Tables\Columns\TextColumn::make('date_out')
                ->label("Date de Sortie")
                ->toggleable(isToggledHiddenByDefault: false)
                ->searchable(),
                Tables\Columns\TextColumn::make('profit')
                    ->numeric()
                    ->label("Bénéfice")
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_total_sale')
                    ->numeric()
                    ->label("Montant Total Inv")
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_total_purchase')
                    ->numeric()
                    ->label("Montant Total Vente")
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('observation')
                    ->label("Observation")
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('shop.name')
                    ->numeric()
                    ->label(label: "Boutique")
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label(label: "Date De Creation")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->label(label: "Date De Modification")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                DateFilter::make('date_out')->label("Date de Sortie")
               
            ]) 
            
            
            ->headerActions([
                Tables\Actions\CreateAction::make() ->modalWidth('7xl'),
            ])
            ->actions([
              ActionGroup::make([
                Tables\Actions\EditAction::make() ->modalWidth('7xl'),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make()->url(fn ($record) => route('filament.admin.stock-out.resources.outs.view', ['record' => $record->id])),
              ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
