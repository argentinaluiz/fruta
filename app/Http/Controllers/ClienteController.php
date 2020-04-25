<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Estado;
use App\Perfil;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Cliente::with('perfilModel');
        if($request->get('cliente')){
            $query->where('id', $request->cliente);
        }
        $clientes = $query->paginate();
        $allClientes = Cliente::orderBy('nome', 'asc')->get(['id', 'nome']);
        return view('cliente.index', compact('clientes', 'allClientes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $estados = Estado::all();
        $estadoDefault = Estado::where('sigla', 'GO')->first();
        $perfis = Perfil::all();
        return view('cliente.create', [
            'estados' => $estados, 'perfis' => $perfis, 'estadoDefault' => $estadoDefault
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nome' => 'required',
            'email' => 'required|email|unique:pessoa,email',
            'perfil' => 'required|exists:perfil,id',
            'endereco' => 'nullable',
            'bairro' => 'required',
            'id_cidade' => 'required|exists:cidade,id',
            'cep' => 'nullable',
            'telefone' => 'nullable',
        ]);

        Cliente::create($request->all());
        return redirect(route('clientes.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Cliente $cliente)
    {
        $estados = Estado::all();
        $perfis = Perfil::all();
        return view('cliente.edit', [
            'cliente' => $cliente,
            'estados' => $estados,
            'perfis' => $perfis,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cliente $cliente)
    {
        $this->validate($request, [
            'nome' => 'required',
            'email' => 'required|email|unique:pessoa,email,' . $cliente->id,
            'perfil' => 'required|exists:perfil,id',
            'endereco' => 'nullable',
            'bairro' => 'required',
            'id_cidade' => 'required|exists:cidade,id',
            'cep' => 'nullable',
            'telefone' => 'nullable',
        ]);

        /** @var Pedido $cliente */
        $cliente->fill($request->all());
        $cliente->save();
        return redirect(route('clientes.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cliente $cliente)
    {
        if ($cliente->pedidos()->count()) {
            abort(403,'Cliente relacionado com pedidos, não é possível excluir');
        }
        $cliente->delete();
        return redirect(route('clientes.index'));
    }
}
