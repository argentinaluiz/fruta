<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedido';

    protected $fillable = ['dataEntrega','dataPedido','dataPedido','idpessoa', 'idorigem'];

    protected $dates = ['dataPedido', 'dataEntrega'];

    public $timestamps = false;

    public function cliente(){
        return $this->belongsTo(Cliente::class,'idpessoa');
    }

    public function perfil(){
        return $this->belongsTo(Perfil::class, 'idorigem');
    }

    public function items(){
        return $this->hasMany(PedidoItem::class,'idpedido');
    }

    public function getItemsNomeAttribute(){
        $itemsNome = [];
        foreach ($this->items as $item){
            $itemsNome[] = "{$item->item->item->item} {$item->item->volume} - {$item->quantidade}";
        }
        return implode('<br>',$itemsNome);
    }
}
