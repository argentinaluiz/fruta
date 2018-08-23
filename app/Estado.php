<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estado';

    public function cidades(){
        return $this->hasMany(Cidade::class,'id_estado');
    }
}
