<?php

namespace App\Filament\Clusters\StockOut\Resources\StockResource\Pages;

use App\Filament\Clusters\StockOut\Resources\StockResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStock extends ViewRecord
{
    protected static string $resource = StockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
