<?php

namespace App\Filament\Clusters\StockOut\Resources\OutResource\Widgets;

use App\Models\Out;
use Filament\Widgets\ChartWidget;

class OutChart extends ChartWidget
{
    protected static ?string $heading = 'Statistiques des sorties';

    protected function getData(): array
    {
        $data = Out::selectRaw('MONTH(date_out) as month, SUM(amount_total_sale) as total_sales')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $datasets = $data->pluck('total_sales')->toArray();
        $labels = [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Ventes totales',
                    'data' => $datasets,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
