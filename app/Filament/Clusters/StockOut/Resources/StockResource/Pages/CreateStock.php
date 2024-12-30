<?php

namespace App\Filament\Clusters\StockOut\Resources\StockResource\Pages;

use App\Filament\Clusters\StockOut\Resources\StockResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStock extends CreateRecord
{
    protected static string $resource = StockResource::class;
}
