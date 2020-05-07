<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\ItemValor;
use App\Pedido;
use App\Perfil;
use App\Troca;
use Carbon\Carbon;
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
        $pedidoQuery = $this->addClient(Pedido::query(), $request);

        if ($request->get('data_inicio')) {
            $pedidoQuery->greaterThanDataPedido($request->data_inicio);
        }
        if ($request->get('data_fim')) {
            $pedidoQuery->lessThanDataPedido($request->data_fim);
        }

        $pedidoTotalQuery = clone $pedidoQuery;
        $pedidoTotalEntregaQuery = clone $pedidoQuery;
        $pedidos = $pedidoQuery->orderBy('pedido.id', 'desc')->paginate();
        $totalTrocas = $this->addClient(Troca::query(), $request)->count();

        $total = $pedidoTotalQuery->withTotal()->first()->total_pedido;
        $total = $total ?? 0;
        $totalTaxaEntrega = $pedidoTotalEntregaQuery->withTotalTaxaEntrega()->first()->total_taxa_entrega;
        $totalTaxaEntrega = $totalTaxaEntrega ?? 0;

        $totalToday = $this->findTotalBetween([
            (new Carbon())->format('Y-m-d'),
            (new \DateTime())->format('Y-m-d')
        ], $request);

        $totalEntregaToday = $this->findTotalEntregaBetween([
            (new Carbon())->format('Y-m-d'),
            (new \DateTime())->format('Y-m-d')
        ], $request);

        $totalSevenDays = $this->findTotalBetween([
            (new Carbon())->subDays(7)->format('Y-m-d'),
            (new \DateTime())->format('Y-m-d')
        ], $request);

        $totalEntregaSevenDays = $this->findTotalEntregaBetween([
            (new Carbon())->subDays(7)->format('Y-m-d'),
            (new \DateTime())->format('Y-m-d')
        ], $request);

        $totalThirtyDays = $this->findTotalBetween([
            (new Carbon())->subDays(30)->format('Y-m-d'),
            (new \DateTime())->format('Y-m-d')
        ], $request);

        $totalEntregaThirtyDays = $this->findTotalEntregaBetween([
            (new Carbon())->subDays(30)->format('Y-m-d'),
            (new \DateTime())->format('Y-m-d')
        ], $request);

        $clientes = Cliente::orderBy('nome', 'asc')->get(['id', 'nome']);

        return view('pedido.index', compact(
            'pedidos',
            'clientes',
            'total',
            'totalTaxaEntrega',
            'totalToday',
            'totalEntregaToday',
            'totalSevenDays',
            'totalEntregaSevenDays',
            'totalThirtyDays',
            'totalEntregaThirtyDays',
            'totalTrocas'
        ));
    }

    protected function findTotalBetween($interval, $request)
    {
        $total = $this->addClient(Pedido::query(), $request)
            ->withTotal()
            ->beetweenDataPedido([
                $interval[0],
                $interval[1]
            ])
            ->first()->total_pedido;
        return $total ?? 0;
    }

    protected function findTotalEntregaBetween($interval, $request)
    {
        $total = $this->addClient(Pedido::query(), $request)
            ->withTotalTaxaEntrega()
            ->beetweenDataPedido([
                $interval[0],
                $interval[1]
            ])
            ->first()->total_taxa_entrega;
        return $total ?? 0;
    }

    protected function addClient($query, Request $request)
    {
        $cliente = $request->get('cliente');
        return $cliente && $cliente !== "" ? $query->byCliente($cliente) : $query;
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
            'idpessoa' => $request->cliente,
            'idorigem' => $request->perfil,
            'taxa_entrega' => $request->taxa_entrega,
            'contabilizar_entrega' => $request->has('contabilizar_entrega'),
        ]);
        foreach ($request->items as $item) {
            $pedido->items()->create([
                'iditem' => $item['item'],
                'quantidade' => $item['quantidade'],
                'valor' => ItemValor::find($item['item'])->valor
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
                'quantidade' => $item->quantidade,
                'valor' => $item->valor,
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
                'quantidade' => $item['quantidade'],
                'valor' => ItemValor::find($item['item'])->valor
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
