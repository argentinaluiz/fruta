<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedido';

    protected $fillable = ['dataEntrega','dataPedido','dataPedido','idpessoa'];

    protected $dates = ['dataPedido', 'dataEntrega'];

    public $timestamps = false;

    public function cliente(){
        return $this->belongsTo(Cliente::class,'idpessoa');
    }

    public function items(){
        return $this->hasMany(PedidoItem::class,'idpedido');
    }

    public function getItemsNomeAttribute(){
        $itemsNome = [];
        foreach ($this->items as $item){
            $itemsNome[] = "{$item->item->item->item} - {$item->item->volume}";
        }
        return implode(',',$itemsNome);
    }
}
