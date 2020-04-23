<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\ItemValor;
use App\Pedido;
use App\Perfil;
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

        $this->addClient($pedidoQuery, $request);

        if ($request->get('data_inicio')) {
            $pedidoQuery->whereDate('dataPedido', '>=', $request->data_inicio);
        }
        if ($request->get('data_fim')) {
            $pedidoQuery->whereDate('dataPedido', '<=', $request->data_fim);
        }
        $pedidoTotalQuery = clone $pedidoQuery;
        $pedidoTotalEntregaQuery = clone $pedidoQuery;

        $pedidos = $pedidoQuery->orderBy('id', 'desc')->paginate();
        $total = $this->makeQuery($pedidoTotalQuery)->first()->total;
        $total = $total ?? 0;
        $totalTaxaEntrega = $pedidoTotalEntregaQuery
            ->selectRaw('sum(taxa_entrega) as total')
            ->first()->total;
        $totalTaxaEntrega = $totalTaxaEntrega ?? 0;

        $totalToday = $this->makeQuery($this->addClient(Pedido::query(), $request))
            ->whereBetween('dataPedido', [
                (new \Carbon\Carbon())->format('Y-m-d'),
                (new \DateTime())->format('Y-m-d')
            ])
            ->first()->total;
        $totalToday = $totalToday ?? 0;

        $totalSevenDays = $this->makeQuery($this->addClient(Pedido::query(), $request))
            ->whereBetween('dataPedido', [
                (new \Carbon\Carbon())->subDays(7)->format('Y-m-d'),
                (new \DateTime())->format('Y-m-d')
            ])
            ->first()->total;
        $totalSevenDays = $totalSevenDays ?? 0;

        $totalThirtyDays = $this->makeQuery($this->addClient(Pedido::query(), $request))
            ->whereBetween('dataPedido', [
                (new \Carbon\Carbon())->subDays(30)->format('Y-m-d'),
                (new \DateTime())->format('Y-m-d')
            ])
            ->first()->total;
        $totalThirtyDays = $totalThirtyDays ?? 0;

        $clientes = Cliente::all();

        return view('pedido.index', compact(
            'pedidos', 'clientes', 'total','totalTaxaEntrega', 'totalSevenDays', 'totalThirtyDays', 'totalToday'
        ));
    }

    protected function makeQuery($query)
    {
        return $query
            ->selectRaw('sum(item_pedido.quantidade*item_valor_tamanho.valor) as total')
            ->join('item_pedido', 'pedido.id', '=', 'item_pedido.idpedido')
            ->join('item_valor_tamanho', 'item_valor_tamanho.id', '=', 'item_pedido.iditem');
    }

    protected function addClient($query, Request $request)
    {
        $cliente = $request->get('cliente');
        return $cliente && $cliente !== "" ? $query->where('idpessoa', $cliente) : $query;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clientes = Cliente::all();
        $perfis = Perfil::all();
        $items = ItemValor::with('item')->get()->map(function ($item) {
            return ['label' => "{$item->item->item} - {$item->volume} - R$ {$item->valor}", 'value' => $item];
        });
        return view('pedido.create', [
            'clientes' => $clientes,
            'perfis' => $perfis,
            'items' => $items
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'dataPedido' => 'nullable|date',
            'dataEntrega' => 'nullable|date',
            'cliente' => 'required|exists:pessoa,id',
            'perfil' => 'required|exists:perfil,id',
            'taxa_entrega' => 'required|numeric',
            'items' => 'required|array',
            'items.*.item' => 'required|exists:item_valor_tamanho,id',
            'items.*.quantidade' => 'required|integer|min:1',
        ]);

        /** @var Pedido $pedido */
        $pedido = Pedido::create([
            'dataPedido' => $request->dataPedido,
            'dataEntrega' => $request->dataEntrega,
            'idpessoa' => $request->cliente,
            'idorigem' => $request->perfil,
            'taxa_entrega' => $request->taxa_entrega,
            'contabilizar_entrega' => $request->has('contabilizar_entrega'),
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Pedido $pedido)
    {
        $clientes = Cliente::all();
        $perfis = Perfil::all();
        $items = ItemValor::with('item')->get()->map(function ($item) {
            return ['label' => "{$item->item->item} - {$item->volume} - R$ {$item->valor}", 'value' => $item];
        });
        $itemsDoPedido = [];
        foreach ($pedido->items as $item) {
            $itemsDoPedido[] = [
                'selected' => [
                    'label' => "{$item->item->item->item} - {$item->item->volume} - R$ {$item->item->valor}",
                    'value' => $item->item
                ],
                'quantidade' => $item->quantidade
            ];
        }

        return view('pedido.edit', [
            'pedido' => $pedido,
            'clientes' => $clientes,
            'perfis' => $perfis,
            'items' => $items,
            'itemsDoPedido' => $itemsDoPedido
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pedido $pedido)
    {
        $this->validate($request, [
            'dataPedido' => 'nullable|date',
            'dataEntrega' => 'nullable|date',
            'cliente' => 'required|exists:pessoa,id',
            'perfil' => 'required|exists:perfil,id',
            'taxa_entrega' => 'required|numeric',
            'items' => 'required|array',
            'items.*.item' => 'required|exists:item_valor_tamanho,id',
            'items.*.quantidade' => 'required|integer|min:1',
        ]);

        /** @var Pedido $pedido */
        $pedido->fill([
            'dataPedido' => $request->dataPedido,
            'dataEntrega' => $request->dataEntrega,
            'idpessoa' => $request->cliente,
            'idorigem' => $request->perfil,
            'taxa_entrega' => $request->taxa_entrega,
            'contabilizar_entrega' => $request->has('contabilizar_entrega'),
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
