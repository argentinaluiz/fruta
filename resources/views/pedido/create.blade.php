@extends('layouts.app')

@section('content')
    <div class="container" id="container-pedido">
        <div class="row">
            <h4>Novo pedido</h4>
            <div class="col-md-12">
                <form id="my-form" action="{{route('pedidos.store')}}" method="post" @submit.prevent="onsubmit()">
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
                        <label for="perfil" class="form-l">Origem</label>
                        <select class="form-control" name="perfil" id="perfil" required>
                            <option value="">Selecione o perfil</option>
                            @foreach($perfis as $perfil)
                                <option value="{{$perfil->id}}">{{$perfil->nome}}</option>
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
                                <th style="width: 60%">Item</th>
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

@push('stylesheet')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css">
@endpush

@push('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.17/vue.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue-select/2.4.0/vue-select.js"></script>
    <script>
        Vue.config.delimiters = ['[[', ']]'];
        Vue.component('v-select', VueSelect.VueSelect);
        var app = new Vue({
            el: '#container-pedido',
            data: {
                items: [{selected: null}],
                selected: null,
            },
            methods: {
                adicionarItem: function(){
                    this.items.push([]);
                },
                removerLinha: function(index){
                    this.items.splice(index,1);
                },
                onsubmit(){
                    for(var key in this.items){
                        $('[name="items['+key+'][item]"]').val(this.items[key].selected.value.id);
                    }
                    $('#my-form')[0].submit();
                }
            }
        });
    </script>
@endpush