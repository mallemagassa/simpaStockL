<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{

    protected $fillable = [
        'name',
        'address',
        'logo_shop',
        'user_id',
    ];


    public function outs(){

        return $this->hasMany(Out::class);
    }

    public function user(){

        return $this->belongsTo(User::class);
    }


}
