<?php

// namespace App\Filament\Clusters\StockOut\Resources;

// use Filament\Forms;
// use Filament\Tables;
// use App\Models\OutItem;
// use App\Models\OutItems;
// use Filament\Forms\Form;
// use Filament\Tables\Table;
// use Filament\Resources\Resource;
// use App\Filament\Clusters\StockOut;
// use App\Filament\Imports\OutItemImporter;
// use Filament\Tables\Actions\ImportAction;
// use Illuminate\Database\Eloquent\Builder;
// use App\Filament\Imports\OutItemsImporter;
// use Illuminate\Database\Eloquent\SoftDeletingScope;
// use App\Filament\Clusters\StockOut\Resources\OutItemsResource\Pages;
// use App\Filament\Clusters\StockOut\Resources\OutItemsResource\RelationManagers;

// class OutItemsResource extends Resource
// {
//     protected static ?string $model = OutItem::class;

//     protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

//     protected static ?string $cluster = StockOut::class;

//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([
//                 //
//             ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->headerActions([
//                 // ExportAction::make()
//                 //     ->exporter(OutItemExporter::class),
//                 ImportAction::make()
//                     ->importer(OutItemImporter::class)
//             ])
//             ->columns([
//                 //
//             ])
//             ->filters([
//                 //
//             ])
//             ->actions([
//                 Tables\Actions\EditAction::make(),
//             ])
//             ->bulkActions([
//                 Tables\Actions\BulkActionGroup::make([
//                     Tables\Actions\DeleteBulkAction::make(),
//                 ]),
//             ]);
//     }

//     public static function getRelations(): array
//     {
//         return [
//             //
//         ];
//     }

//     public static function getPages(): array
//     {
//         return [
//             'index' => Pages\ListOutItems::route('/'),
//             'create' => Pages\CreateOutItems::route('/create'),
//             'edit' => Pages\EditOutItems::route('/{record}/edit'),
//         ];
//     }
// }
