<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemValor extends Model
{
    protected $table = 'item_valor_tamanho';

    public function item(){
        return $this->belongsTo(Item::class,'id_item');
    }
}
