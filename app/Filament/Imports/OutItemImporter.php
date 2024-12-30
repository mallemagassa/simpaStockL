<?php

namespace App\Filament\Imports;

use App\Models\Stock;
use App\Models\OutItem;
use App\Models\Product;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;

class OutItemImporter extends Importer
{
    protected static ?string $model = OutItem::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('quantity')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('total')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('out_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('product')
            ->relationship(resolveUsing: function (string $state): ?Product {
                return Stock::query()
                    ->where('id', $state)
                    ->first()->product;
            }),
        ];
    }

    public function resolveRecord(): ?OutItem
    {
        // return OutItem::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new OutItem();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your out item import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
