@extends('layouts.app')

@section('content')
    <div class="container" id="container-pedido">
        <div class="row">
            <h4>Editar pedido #{{$pedido->id}}</h4>
            <div class="col-md-12">
                <form id="my-form" action="{{route('pedidos.update', ['pedido' => $pedido->id])}}" method="post" @submit.prevent="onsubmit">
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
                        <label for="perfil" class="form-l">Origem</label>
                        <select class="form-control" name="perfil" id="perfil" required value="{{$pedido->idorigem}}">
                            <option value="">Selecione o perfil</option>
                            @foreach($perfis as $perfil)
                                <option value="{{$perfil->id}}" {{$perfil->id==$pedido->idorigem?'selected="selected"': ''}}>{{$perfil->nome}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="data_pedido">Data Pedido</label>
                        <input class="form-control" type="date" name="dataPedido" id="data_pedido"
                               value="{{$pedido->dataPedido ? $pedido->dataPedido->format('Y-m-d'):''}}">
                    </div>
                    <div class="form-group">
                        <label for="data_entrega">Data Entrega</label>
                        <input class="form-control" type="date" name="dataEntrega" id="data_entrega"
                               value="{{$pedido->dataEntrega ? $pedido->dataEntrega->format('Y-m-d'): ''}}">
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
                                    <v-select v-model="items[key].selected" :options='{!! json_encode($items) !!}'></v-select>
                                    <input type="hidden" :name="'items['+key+'][item]'">
                                </td>
                                <td>
                                    <input class="form-control" type="number" :name="'items['+key+'][quantidade]'"
                                           step="1" min="1" :value="item.quantidade"/>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue-select/2.4.0/vue-select.js"></script>
    <script>
        Vue.component('v-select', VueSelect.VueSelect);
        Vue.config.delimiters = ['[[', ']]'];
        var app = new Vue({
            el: '#container-pedido',
            data: {
                items: {!! json_encode($itemsDoPedido) !!}
            },
            methods: {
                adicionarItem: function () {
                    this.items.push([]);
                },
                removerLinha: function (index) {
                    this.items.splice(index, 1);
                },
                onsubmit(){
                    for(var key in this.items){
                        $('[name="items['+key+'][item]"]').val(this.items[key].selected.value.id);
                    }
                    $('#my-form')[0].submit();
                }
            }
        })
    </script>
@endpush