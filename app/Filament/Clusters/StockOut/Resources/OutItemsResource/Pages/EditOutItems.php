<?php

namespace App\Filament\Clusters\StockOut\Resources\OutItemsResource\Pages;

use App\Filament\Clusters\StockOut\Resources\OutItemsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOutItems extends EditRecord
{
    protected static string $resource = OutItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
