<?php

namespace App\Filament\Clusters\StockOut\Resources\OutResource\Widgets;

use App\Models\Out;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class OutOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProfit = Out::sum('profit'); // Calcule la somme des profits
        $totalSales = Out::sum('amount_total_sale'); // Somme des ventes totales
        $totalPurchases = Out::sum('amount_total_purchase'); // Somme des Inventaire totaux

        return [
            Stat::make("Total des Inventaire", number_format($totalPurchases, 0, ',', ' ') . ' FCFA')
                ->description('Somme totale des Inv réalisés.')
                ->descriptionIcon('heroicon-m-arrow-trending-down', IconPosition::Before)
                ->color('success'),
                // ->chart($this->getPurchasesChartData()), // Générer les données du graphique pour les Inventaire
            Stat::make("Total des Ventes", number_format($totalSales, 0, ',', ' ') . ' FCFA')
                ->description('Somme totale des ventes réalisées.')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('success'),
                // ->chart($this->getSalesChartData()), // Générer les données du graphique pour les ventes
            Stat::make("Bénéfice total", number_format($totalProfit, 0, ',', ' ') . ' FCFA')
                ->description('Somme totale des bénéfices réalisés.')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('success')
                // ->chart($this->getProfitChartData()), // Générer les données du graphique
        ];
    }

    protected function getSalesChartData(): array
    {
        return Out::selectRaw('SUM(amount_total_sale) as total, MONTH(date_out) as month')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total')
            ->toArray();
    }

    protected function getPurchasesChartData(): array
    {
        return Out::selectRaw('SUM(amount_total_purchase) as total, MONTH(date_out) as month')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total')
            ->toArray();
    }

    protected function getProfitChartData(): array
    {
        return Out::selectRaw('SUM(profit) as total, MONTH(date_out) as month')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total')
            ->toArray();
    }
}

