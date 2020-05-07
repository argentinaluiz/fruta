<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedido';

    protected $fillable = [
        'dataPedido',
        'dataPedido',
        'idpessoa',
        'idorigem',
        'taxa_entrega',
        'contabilizar_entrega'
    ];

    protected $dates = ['dataPedido'];

    public $timestamps = false;

    protected $casts = [
        'contabilizar_entrega' => 'boolean'
    ];

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

    public function getTotalAttribute(){
        $sum = 0;
        foreach ($this->items as $item){
            $sum += $item->valor * $item->quantidade;
        }
        return number_format($sum, 2);
    }

    public function scopeWithTotal($query){
        return $query->selectRaw('sum(item_pedido.quantidade*item_pedido.valor) as total_pedido')
            ->join('item_pedido', 'pedido.id', '=', 'item_pedido.idpedido');
    }

    public function scopeWithTotalTaxaEntrega($query){
        return $query->selectRaw('sum(taxa_entrega) as total_taxa_entrega');
    }

    public function scopeGreaterThanDataPedido($query, $date){
        return $query->whereDate('dataPedido', '>=', $date);
    }

    public function scopeLessThanDataPedido($query, $date){
        return $query->whereDate('dataPedido', '<=', $date);
    }

    public function scopeBeetweenDataPedido($query, $interval){
        return $query->whereBetween('dataPedido', [
            $interval[0],
            $interval[1]
        ]);
    }

    public function scopeByCliente($query, $cliente){
        return $query->where('idpessoa', $cliente);
    }
}
