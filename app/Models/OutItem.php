<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutItem extends Model
{
    protected $fillable = [
        'quantity',
        'total',
        'product_id',
        'out_id',
    ];


    public function out(){

        return $this->BelongsTo(Out::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
