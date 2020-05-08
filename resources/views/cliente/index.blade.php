@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <h4>Listagem de clientes</h4>
            <table class="table">
                <thead>
                <tr>
                    <td colspan="5">
                        <a href="{{route('clientes.create')}}" class="btn btn-primary">Novo cliente</a>
                    </td>
                </tr>
                <tr>
                    <td colspan="7">
                        <form class="form-inline" method="get">
                            <div class="col-auto">
                                <label class="form-con">Cliente</label>
                                <select class="form-control" name="cliente" id="cliente"
                                        value="{{\Request::get('cliente')}}">
                                    <option value="">Todos os clientes</option>
                                    @foreach($allClientes as $cliente)
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
                    <th>Nome</th>
                    <th>Perfil</th>
                    <th>Endereco</th>
                    <th>Bairro</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->id }}</td>
                        <td>{{$cliente->nome}}</td>
                        <td>{{ $cliente->perfilModel->nome }}</td>
                        <td>{{$cliente->endereco }}</td>
                        <td>{{$cliente->bairro}}</td>
                        <td>{{$cliente->telefone }}</td>
                        <td>{{$cliente->email }}</td>
                        <td>
                            <a href="{{route('clientes.edit',['cliente' => $cliente])}}">Editar</a> |
                            <a href="{{route('clientes.destroy',['cliente' => $cliente])}}"
                               onclick="event.preventDefault();if(confirm('Deseja excluir?')){document.getElementById('form-delete-{{$cliente->id}}').submit()}">
                                Excluir
                            </a>
                            <form id='form-delete-{{$cliente->id}}' style="display: none" method="post"
                                  action="{{route('clientes.destroy',['cliente' => $cliente])}}">
                                {{csrf_field()}}
                                {{method_field('DELETE')}}
                            </form>
                            |
                            <a href="{{route('pedidos.index',['cliente' => $cliente->id])}}">Pedidos</a>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $clientes->links() !!}
        </div>
    </div>
@endsection
