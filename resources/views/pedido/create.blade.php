@extends('layouts.app')

@section('content')
    <div class="container" id="container-pedido">
        <div class="row">
            <h4>Novo pedido</h4>
            <div class="col-md-12">
                <form action="{{route('pedidos.store')}}" method="post">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="cliente" class="form-l">Cliente</label>
                        <select class="form-control" name="cliente" id="cliente" required>
                            @foreach($clientes as $cliente)
                                <option value="{{$cliente->id}}">{{$cliente->nome}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="data_pedido">Data Pedido</label>
                        <input class="form-control" type="date" name="dataPedido" id="data_pedido">
                    </div>
                    <div class="form-group">
                        <label for="data_entrega">Data Entrega</label>
                        <input class="form-control" type="date" name="dataEntrega" id="data_entrega">
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
                                    <select class="form-control" :name="'items['+key+'][item]'">
                                        @foreach($items as $item)
                                            <option value="{{$item->id}}">{{$item->item->item}} - {{$item->volume}} - R$ {{$item->valor}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control" type="number" :name="'items['+key+'][quantidade]'" step="1" min="1"/>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-primary">Criar</button>
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
                items: []
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