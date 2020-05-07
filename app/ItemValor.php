<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemValor extends Model
{
    protected $table = 'item_valor_tamanho';
    protected $fillable = ['valor', 'volume'];
    public $timestamps = false;

    public function item(){
        return $this->belongsTo(Item::class,'id_item');
    }
}
