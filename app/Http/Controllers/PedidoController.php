<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Item;
use App\ItemValor;
use App\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pedidoQuery = Pedido::query();
        if($request->get('data_inicio')){
            $pedidoQuery->whereDate('dataPedido', '>=', $request->data_inicio);
        }
        if($request->get('data_fim')){
            $pedidoQuery->whereDate('dataPedido', '<=', $request->data_fim);
        }
        $pedidos = $pedidoQuery->paginate();
        return view('pedido.index', ['pedidos' => $pedidos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clientes = Cliente::all();
        $items = ItemValor::with('item')->get();
        return view('pedido.create', [
            'clientes' => $clientes,
            'items' => $items
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
            'dataEntrega' => 'nullable|date',
            'cliente' => 'required|exists:pessoa,id',
            'items' => 'required|array',
            'items.*.item' => 'required|exists:item,id',
            'items.*.quantidade' => 'required|integer|min:1',
        ]);

        /** @var Pedido $pedido */
        $pedido = Pedido::create([
            'dataEntrega' => $request->dataEntrega,
            'idpessoa' => $request->cliente
        ]);
        foreach ($request->items as $item) {
            $pedido->items()->create([
                'iditem' => $item['item'],
                'quantidade' => $item['quantidade']
            ]);
        }
        return redirect(route('pedidos.index'));
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
    public function edit(Pedido $pedido)
    {
        $clientes = Cliente::all();
        $items = ItemValor::with('item')->get();
        $itemsDoPedido = [];
        foreach ($pedido->items as $item){
            $itemsDoPedido[] = [
                'item' => $item->item->id,
                'quantidade' => $item->quantidade
            ];
        }
        return view('pedido.edit',[
            'pedido' => $pedido,
            'clientes' => $clientes,
            'items' => $items,
            'itemsDoPedido' => $itemsDoPedido
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pedido $pedido)
    {
        $this->validate($request, [
            'dataEntrega' => 'nullable|date',
            'cliente' => 'required|exists:pessoa,id',
            'items' => 'required|array',
            'items.*.item' => 'required|exists:item,id',
            'items.*.quantidade' => 'required|integer|min:1',
        ]);

        /** @var Pedido $pedido */
        $pedido->fill([
            'dataEntrega' => $request->dataEntrega,
            'idpessoa' => $request->cliente
        ]);
        $pedido->save();
        $pedido->items()->delete();
        foreach ($request->items as $item) {
            $pedido->items()->create([
                'iditem' => $item['item'],
                'quantidade' => $item['quantidade']
            ]);
        }
        return redirect(route('pedidos.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
