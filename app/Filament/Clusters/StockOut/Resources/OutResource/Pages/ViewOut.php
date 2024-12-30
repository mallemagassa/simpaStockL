<?php

namespace App\Filament\Clusters\StockOut\Resources\OutResource\Pages;

use App\Filament\Clusters\StockOut\Resources\OutResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOut extends ViewRecord
{
    protected static string $resource = OutResource::class; //waybill-out

    protected static string $view = 'pages.waybill-out';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->icon('heroicon-o-pencil'),
            Actions\DeleteAction::make()->icon('heroicon-o-trash'),
            Actions\Action::make('print')
                ->label('Imprimer')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action(function (){
                    $this->js('window.print()');
                }),
        ];
    }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\EditAction::make(),
    //     ];
    // }
}
