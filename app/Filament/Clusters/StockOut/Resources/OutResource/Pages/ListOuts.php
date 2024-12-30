<?php

namespace App\Filament\Clusters\StockOut\Resources\OutResource\Pages;

use App\Filament\Clusters\StockOut\Resources\OutResource;
use App\Filament\Clusters\StockOut\Resources\OutResource\Widgets\OutOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOuts extends ListRecords
{
    protected static string $resource = OutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OutOverview::class   
        ];
    }
}
