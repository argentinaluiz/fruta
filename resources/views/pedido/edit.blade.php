@extends('layouts.app')

@section('content')
    <div class="container" id="container-pedido">
        <div class="row">
            <h4>Editar pedido #{{$pedido->id}}</h4>
            <div class="col-md-12">
                <form action="{{route('pedidos.update', ['pedido' => $pedido->id])}}" method="post">
                    {{csrf_field()}}
                    {{method_field('PUT')}}
                    <div class="form-group">
                        <label for="cliente" class="form-l">Cliente</label>
                        <select class="form-control" name="cliente" id="cliente" required value="{{$pedido->idpessoa}}">
                            @foreach($clientes as $cliente)
                                <option value="{{$cliente->id}}" {{$cliente->id==$pedido->idpessoa?'selected="selected"': ''}}>{{$cliente->nome}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="data_pedido">Data Pedido</label>
                        <input class="form-control" type="date" disabled value="{{$pedido->dataPedido->format('Y-m-d')}}">
                    </div>
                    <div class="form-group">
                        <label for="data_entrega">Data Entrega</label>
                        <input class="form-control" type="date" name="dataEntrega" id="data_entrega" value="{{$pedido->dataEntrega ? $pedido->dataEntrega->format('Y-m-d'): ''}}">
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary" @click="adicionarItem()">Nova linha</button>
                        <table class="table">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Item</th>
                                <th>Quant</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item,key) in items">
                                <td>
                                    <button type="button" @click="removerLinha(key)">Remover</button>
                                </td>
                                <td>
                                    <select class="form-control" :name="'items['+key+'][item]'" :value="item.item">
                                        @foreach($items as $item)
                                            <option value="{{$item->id}}">{{$item->item}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control" type="number" :name="'items['+key+'][quantidade]'" step="1" min="1" :value="item.quantidade"/>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.17/vue.min.js"></script>
    <script>
        Vue.config.delimiters = ['[[', ']]'];
        var app = new Vue({
            el: '#container-pedido',
            data: {
                items: {!! json_encode($itemsDoPedido) !!}
            },
            methods: {
                adicionarItem: function(){
                    this.items.push([]);
                },
                removerLinha: function(index){
                    this.items.splice(index,1);
                }
            }
        })
    </script>
@endpush