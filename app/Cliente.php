<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'pessoa';
    protected $fillable = [
        'nome',
        'email',
        'perfil',
        'endereco',
        'bairro',
        'id_cidade',
        'cep',
        'telefone',
    ];
    public $timestamps = false;

    public function perfilModel()
    {
        return $this->belongsTo(Perfil::class, 'perfil');
    }

    public function cidade(){
        return $this->belongsTo(Cidade::class,'id_cidade');
    }

    public function pedidos(){
        return $this->hasMany(Pedido::class,'idpessoa');
    }

    public function trocas(){
        return $this->hasMany(Troca::class, 'idpessoa');
    }
}
