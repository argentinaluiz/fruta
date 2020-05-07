<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'item';
    protected $fillable = [ 'item', 'idcategoria', 'ingredientes'];
    public $timestamps = false;

    public function itemValores(){
        return $this->hasMany(ItemValor::class, 'id_item');
    }

    public function categoria(){
        return $this->belongsTo(Categoria::class,'idcategoria');
    }
}
