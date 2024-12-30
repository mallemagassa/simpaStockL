<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'code',
        'unit_id',
    ];

    // public $incrementing = false;

    public function unit(){

        return $this->BelongsTo(Unit::class);
    }

    public function outItems(){

        return $this->hasMany(OutItem::class);
    }

    public function stock(){

        return $this->hasOne(Stock::class);
    }

}
