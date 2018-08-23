@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <h4>Listagem de pedidos</h4>
            <table class="table">
                <thead>
                <tr>
                    <td>
                        <strong>Total:</strong> R$ {{$total ?? 0}}
                    </td>
                    <td>
                        <strong>Hoje:</strong> R$ {{$totalToday ?? 0}}
                    </td>
                    <td>
                        <strong>Últimos 7 dias:</strong> R$ {{$totalSevenDays ?? 0 }}
                    </td>
                    <td>
                        <strong>Últimos 30 dias:</strong> R$ {{$totalThirtyDays ?? 0}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{route('pedidos.create')}}" class="btn btn-primary" >Novo pedido</a>
                    </td>
                    <td colspan="4">
                        <form class="form-inline" method="get">
                            <div class="col-auto">
                                <input type="date" name="data_inicio" class="form-control" value="{{\Request::get('data_inicio') ? (new \DateTime(\Request::get('data_inicio')))->format('Y-m-d'): ''}}">
                            </div>
                            <div class="col-auto">
                                <input type="date" name="data_fim" class="form-control" value="{{\Request::get('data_fim') ? (new \DateTime(\Request::get('data_fim')))->format('Y-m-d'): ''}}">
                            </div>
                            <button class="btn btn-primary" type="submit">Pesquisar</button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <th>#</th>
                    <th>Items</th>
                    <th>Criado em</th>
                    <th>Entrega</th>
                    <th>Cliente</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($pedidos as $pedido)
                    <tr>
                        <td>{{ $pedido->id }}</td>
                        <td>{!! $pedido->items_nome !!}</td>
                        <td>{{$pedido->dataPedido ? $pedido->dataPedido->format('d/m/Y'): ''}}</td>
                        <td>{{$pedido->dataEntrega ? $pedido->dataEntrega->format('d/m/Y'): ''}}</td>
                        <td>{{$pedido->cliente->nome}}</td>
                        <td>
                            <a href="{{route('pedidos.edit',['pedido' => $pedido])}}">Editar</a> |
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $pedidos->links() !!}
        </div>
    </div>
@endsection