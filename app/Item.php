<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'item';

    public $timestamps = false;

    public function itemValor(){
        return $this->belongsTo(ItemValor::class, 'iditem');
    }
}
