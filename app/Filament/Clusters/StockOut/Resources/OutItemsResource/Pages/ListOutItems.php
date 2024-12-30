<?php

namespace App\Filament\Clusters\StockOut\Resources\OutItemsResource\Pages;

use App\Filament\Clusters\StockOut\Resources\OutItemsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOutItems extends ListRecords
{
    protected static string $resource = OutItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
