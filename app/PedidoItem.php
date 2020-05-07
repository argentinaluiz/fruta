<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    protected $table = 'item_pedido';

    protected $fillable = ['iditem','quantidade', 'valor'];

    public $timestamps = false;

    public function item(){
        return $this->belongsTo(ItemValor::class,'iditem');
    }
}
