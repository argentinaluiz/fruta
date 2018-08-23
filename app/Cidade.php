<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    protected $table = 'cidade';

    public function estado(){
        return $this->belongsTo(Estado::class,'id_estado');
    }
}
