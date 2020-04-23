<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Troca extends Model
{
    protected $table = 'troca';

    public function cliente(){
        return $this->belongsTo(Cliente::class,'idpessoa');
    }

    public function scopeByCliente($query, $cliente){
        return $query->where('idpessoa', $cliente);
    }
}
