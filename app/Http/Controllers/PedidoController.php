<?php

namespace App\Http\Controllers;

use App\Cliente;
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
        if ($request->get('data_inicio')) {
            $pedidoQuery->whereDate('dataPedido', '>=', $request->data_inicio);
        }
        if ($request->get('data_fim')) {
            $pedidoQuery->whereDate('dataPedido', '<=', $request->data_fim);
        }
        $pedidoClonedQuery = clone $pedidoQuery;
        $pedidoClonedQuery->selectRaw('sum(item_pedido.quantidade*item_valor_tamanho.valor) as total')
            ->join('item_pedido', 'pedido.id', '=', 'item_pedido.idpedido')
            ->join('item_valor_tamanho', 'item_valor_tamanho.id', '=', 'item_pedido.iditem');
        $total = $pedidoClonedQuery->first()->total;

        $totalToday = Pedido::selectRaw('sum(item_pedido.quantidade*item_valor_tamanho.valor) as total')
            ->join('item_pedido', 'pedido.id', '=', 'item_pedido.idpedido')
            ->join('item_valor_tamanho', 'item_valor_tamanho.id', '=', 'item_pedido.iditem')
            ->whereBetween('dataPedido', [
                (new \Carbon\Carbon())->format('Y-m-d'),
                (new \DateTime())->format('Y-m-d')
            ])
            ->first()->total;

        $totalSevenDays = Pedido::selectRaw('sum(item_pedido.quantidade*item_valor_tamanho.valor) as total')
            ->join('item_pedido', 'pedido.id', '=', 'item_pedido.idpedido')
            ->join('item_valor_tamanho', 'item_valor_tamanho.id', '=', 'item_pedido.iditem')
            ->whereBetween('dataPedido', [
                (new \Carbon\Carbon())->subDays(7)->format('Y-m-d'),
                (new \DateTime())->format('Y-m-d')
            ])
            ->first()->total;

        $totalThirtyDays = Pedido::selectRaw('sum(item_pedido.quantidade*item_valor_tamanho.valor) as total')
            ->join('item_pedido', 'pedido.id', '=', 'item_pedido.idpedido')
            ->join('item_valor_tamanho', 'item_valor_tamanho.id', '=', 'item_pedido.iditem')
            ->whereBetween('dataPedido', [
                (new \Carbon\Carbon())->subDays(30)->format('Y-m-d'),
                (new \DateTime())->format('Y-m-d')
            ])
            ->first()->total;
        $pedidos = $pedidoQuery->orderBy('id','desc')->paginate();
        return view('pedido.index', compact(
            'pedidos', 'total', 'totalSevenDays', 'totalThirtyDays', 'totalToday'
        ));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clientes = Cliente::all();
        $items = ItemValor::with('item')->get()->map(function ($item) {
            return ['label' => "{$item->item->item} - {$item->volume} - R$ {$item->valor}", 'value' => $item];
        });
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
            'dataPedido' => 'nullable|date',
            'dataEntrega' => 'nullable|date',
            'cliente' => 'required|exists:pessoa,id',
            'items' => 'required|array',
            'items.*.item' => 'required|exists:item_valor_tamanho,id',
            'items.*.quantidade' => 'required|integer|min:1',
        ]);

        /** @var Pedido $pedido */
        $pedido = Pedido::create([
            'dataPedido' => $request->dataPedido,
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
        $items = ItemValor::with('item')->get()->map(function ($item) {
            return ['label' => "{$item->item->item} - {$item->volume} - R$ {$item->valor}", 'value' => $item];
        });
        $itemsDoPedido = [];
        foreach ($pedido->items as $item) {
            $itemsDoPedido[] = [
                'selected' => [
                    'label' => "{$item->item->item->item} - {$item->item->volume} - R$ {$item->item->valor}", 'value' => $item->item
                ],
                'quantidade' => $item->quantidade
            ];
        }

        return view('pedido.edit', [
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
            'dataPedido' => 'nullable|date',
            'dataEntrega' => 'nullable|date',
            'cliente' => 'required|exists:pessoa,id',
            'items' => 'required|array',
            'items.*.item' => 'required|exists:item_valor_tamanho,id',
            'items.*.quantidade' => 'required|integer|min:1',
        ]);

        /** @var Pedido $pedido */
        $pedido->fill([
            'dataPedido' => $request->dataPedido,
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
