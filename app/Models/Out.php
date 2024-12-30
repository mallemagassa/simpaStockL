<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Out extends Model
{
    protected $fillable = [
        'date_out',
        'observation',
        'amount_total_sale',
        'amount_total_purchase',
        'profit',
        'ref',
        'shop_id',
    ];

     /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_out' => 'date',
        ];
    }

    public function outItems(){

        return $this->hasMany(OutItem::class);
    }

    public function shop(){

        return $this->BelongsTo(Shop::class);
    }

    
}
