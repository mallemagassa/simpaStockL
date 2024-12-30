<?php

namespace App\Filament\Clusters\StockOut\Resources\OutResource\Pages;

use App\Models\Stock;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Clusters\StockOut\Resources\OutResource;

class EditOut extends EditRecord
{
    protected static string $resource = OutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    // protected function afterSave(): void
    // {
    //     $out = $this->record; // La sortie modifiée
    
    //     // Obtenir les anciennes quantités des articles associés
    //     $originalItems = $out->outItems()->get();
    
    //     // Mettre à jour le stock pour les anciens articles
    //     foreach ($originalItems as $item) {
    //         $stock = Stock::where('product_id', $item->product_id)->first();
    //         if ($stock) {
    //             // Réajuster le stock en ajoutant la quantité initialement sortie
    //             $stock->quantity -= $item->quantity;
    //             $stock->save();
    //         }
    //     }
    
    //     // Mettre à jour les stocks pour les nouveaux articles
    //     // foreach ($out->outItems as $item) {
    //     //     $stock = Stock::where('product_id', $item->product_id)->first();
    //     //     if ($stock) {
    //     //         // Réduire le stock en fonction de la nouvelle quantité sortie
    //     //         $stock->quantity -= $item->quantity;
    //     //         $stock->save();
    //     //     }
    //     // }
    // }

    // protected function handleRecordUpdate(Model $record, array $data): Model
    // {
    //     dd($data);
    //     $record->update($data);
    
    //     return $record;
    // }
}
