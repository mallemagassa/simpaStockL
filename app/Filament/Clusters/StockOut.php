<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class StockOut extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Stock & Sortie';
    protected static ?int $navigationSort = 1;
    protected static ?string $clusterBreadcrumb = 'Stock & Sortie';
}
