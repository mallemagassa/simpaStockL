<?php

namespace App\Filament\Clusters\StockOut\Resources\StockResource\Widgets;

use App\Models\Stock;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StockOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    
    protected function getStats(): array
    {
        
        $totals = Stock::getTotals();

        // dd($totals);

        return [
            Stat::make('Montant total d\'Inventaire', number_format($totals['totalPurchase'], 0, ',', ' ') . ' FCFA')
                ->description($totals['totalPurchase'] > 1000000 ? 'Inventaire élevé.' : 'Inventaire modéré.')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('success'),
                // ->chart($totals['purchaseChart']),
            Stat::make('Montant total du vente', number_format($totals['totalSale'], 0, ',', ' ') . ' FCFA')
                ->description($totals['totalSale'] > 1000000 
                    ? 'Ventes potentielles élevées.' 
                    : 'Ventes potentielles modérées.')
                ->color('success')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                // ->chart($totals['saleChart']),
            
        ];
        
    }
}
