<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    // public $incrementing = false;

    protected $fillable = [
        'purchase_price',
        'sale_price',
        'quantity',
        'critique',
        'product_id',
    ];

    public function product(){

        return $this->belongsTo(Product::class);
    }

    public static function getTotals(): array
    {
        $stocks = self::all();

        $totalPurchase = $stocks->sum(function ($stock) {
            return $stock->purchase_price * $stock->quantity;
        });

        $totalSale = $stocks->sum(function ($stock) {
            return $stock->sale_price * $stock->quantity;
        });

        $purchaseChart = $stocks->pluck('purchase_price')->toArray();
        $saleChart = $stocks->pluck('sale_price')->toArray();

        return [
            'totalPurchase' => $totalPurchase,
            'totalSale' => $totalSale,
            'purchaseChart' => $purchaseChart,
            'saleChart' => $saleChart,
        ];
    }

    public static function getTotalsChart(): array
    {
        // Récupérer tous les stocks avec leurs produits associés
        $stocks = self::with('product')->get();

        // Initialiser les tableaux pour les totaux d'Inventaire, de ventes et les labels
        $purchaseChart = [];
        $saleChart = [];
        $labels = [];

        // Parcourir chaque stock et récupérer les informations nécessaires
        foreach ($stocks as $stock) {
            // Ajouter le total des Inventaire et des ventes pour chaque produit
            $purchaseChart[] = $stock->purchase_price * $stock->quantity;
            $saleChart[] = $stock->sale_price * $stock->quantity;

            // Ajouter le nom du produit comme label
            $labels[] = $stock->product->name; // Supposons que chaque stock est lié à un produit
        }

        // Retourner les données nécessaires pour le graphique
        return [
            'purchaseChart' => $purchaseChart,
            'saleChart' => $saleChart,
            'labels' => $labels, // Les labels sont maintenant dynamiques et basés sur les produits
        ];
    }

}
