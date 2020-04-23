@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <h4>Listagem de pedidos</h4>
            <table class="table">
                <tr>
                    <td>
                        <strong>Quant.:</strong> {{$pedidos->total()}}
                    </td>
                    <td>
                        <strong>Total:</strong> R$ {{$total + $totalTaxaEntrega}} ({{$totalTaxaEntrega}})
                    </td>
                    <td>
                        <strong>Hoje:</strong> R$ {{$totalToday}}
                    </td>
                    <td>
                        <strong>Últimos 7 dias:</strong> R$ {{$totalSevenDays}}
                    </td>
                    <td>
                        <strong>Últimos 30 dias:</strong> R$ {{$totalThirtyDays}}
                    </td>
                </tr>
            </table>
            <table class="table">
                <thead>
                <tr>
                    <td colspan="7">
                        <a href="{{route('pedidos.create')}}" class="btn btn-primary">Novo pedido</a>
                    </td>
                </tr>
                <tr>
                    <td colspan="7">
                        <form class="form-inline" method="get">
                            <div class="col-auto">
                                <label>Início</label>
                                <input type="date" name="data_inicio" class="form-control"
                                       value="{{\Request::get('data_inicio') ? (new \DateTime(\Request::get('data_inicio')))->format('Y-m-d'): ''}}">
                            </div>
                            <div class="col-auto">
                                <label>Fim</label>
                                <input type="date" name="data_fim" class="form-control"
                                       value="{{\Request::get('data_fim') ? (new \DateTime(\Request::get('data_fim')))->format('Y-m-d'): ''}}">
                            </div>

                            <div class="col-auto">
                                <label class="form-con">Cliente</label>
                                <select class="form-control" name="cliente" id="cliente" value="{{\Request::get('cliente')}}">
                                    <option value="">Todos os clientes</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{$cliente->id}}" {{$cliente->id==\Request::get('cliente')?'selected="selected"': ''}}>{{$cliente->nome}}</option>
                                    @endforeach
                                </select>
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
                    <th>Origem</th>
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
                        <td>{{$pedido->perfil ? $pedido->perfil->nome: ''}}</td>
                        <td>
                            <a href="{{route('pedidos.edit',['pedido' => $pedido])}}">Editar</a> |
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $pedidos->appends(request()->query())->links() !!}
        </div>
    </div>
@endsection
