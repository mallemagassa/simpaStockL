<?php

namespace App\Filament\Clusters\StockOut\Resources\StockResource\Widgets;

use App\Models\Stock;
use Filament\Widgets\ChartWidget;

class StockChart extends ChartWidget
{
    protected static ?string $heading = 'Totaux des Inventaire et ventes';

    protected function getData(): array
    {
        // Récupérer les totaux et les données pour les graphiques
        $totals = Stock::getTotalsChart();

        // Données pour le graphique
        return [
            'datasets' => [
                [
                    'label' => 'Total des Inventaire',
                    'data' => $totals['purchaseChart'],
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Total des ventes',
                    'data' => $totals['saleChart'],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $totals['labels'], // Utiliser les labels dynamiques
        ];
    }



    protected function getType(): string
    {
        return 'bar';
    }
}
