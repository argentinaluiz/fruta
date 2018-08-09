<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    const CREATED_AT = 'dataPedido';

    const UPDATED_AT = null;

    protected $table = 'pedido';

    protected $fillable = ['dataEntrega','dataPedido','idpessoa'];

    protected $dates = ['dataPedido', 'dataEntrega'];

    public function cliente(){
        return $this->belongsTo(Cliente::class,'idpessoa');
    }

    public function items(){
        return $this->hasMany(PedidoItem::class,'idpedido');
    }

    public function getItemsNomeAttribute(){
        $itemsNome = [];
        foreach ($this->items as $item){
            $itemsNome[] = $item->item->item;
        }
        return implode(',',$itemsNome);
    }
}
