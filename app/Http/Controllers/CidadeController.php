<?php

namespace App\Http\Controllers;

use App\Estado;

class CidadeController extends Controller
{

    public function getCidades($idEstado)
    {
        $estado = Estado::find($idEstado);
        $cidades = $estado->cidades()->get(['id', 'nome']);
        return response()->json($cidades);
    }

}
