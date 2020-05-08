@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <h4>Listagem de itens</h4>
            <table class="table">
                <thead>
                <tr>
                    <td colspan="7">
                        <a href="{{route('items.create')}}" class="btn btn-primary">Novo item</a>
                    </td>
                </tr>
                <tr>
                    <td colspan="7">
                        <form class="form-inline" method="get">
                            <div class="col-auto">
                                <label class="form-con">Item</label>
                                <select class="form-control" name="item" id="item" value="{{\Request::get('item')}}">
                                    <option value="">Todos os items</option>
                                    @foreach($allItems as $item)
                                        <option value="{{$item->id}}" {{$item->id==\Request::get('item')?'selected="selected"': ''}}>{{$item->item}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-con">Categoria</label>
                                <select class="form-control" name="categoria" id="categoria" value="{{\Request::get('categoria')}}">
                                    <option value="">Todos as categorias</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{$categoria->id}}" {{$categoria->id==\Request::get('categoria')?'selected="selected"': ''}}>{{$categoria->nome}}</option>
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
                    <th>Categoria</th>
                    <th>Ingredientes</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->item }}</td>
                        <td>{{ $item->categoria->nome }}</td>
                        <td>{{$item->ingredientes}}</td>
                        <td>
                            <a href="{{route('items.edit',['item' => $item])}}">Editar</a> |
                            <a href="{{route('items.destroy',['item' => $item])}}"
                               onclick="event.preventDefault();if(confirm('Deseja excluir?')){document.getElementById('form-delete-{{$item->id}}').submit()}">
                                Excluir
                            </a>
                            <form id='form-delete-{{$item->id}}' style="display: none" method="post"
                                  action="{{route('items.destroy',['item' => $item])}}">
                                {{csrf_field()}}
                                {{method_field('DELETE')}}
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $items->appends(request()->query())->links() !!}
        </div>
    </div>
@endsection
