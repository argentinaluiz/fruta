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
                    <div class="row">
                        <div class="col-xl-2">
                            <div class="checkbox">
                                <label>
                                    <input name="contabilizar_entrega" type="checkbox"
                                            {{$pedido->contabilizar_entrega === true ? 'checked="checked"': null}}> Contabilizar entrega
                                </label>
                            </div>
                        </div>
                        <div class="col-xl-10">
                            <div class="form-group">
                                <label for="taxa_entrega">Tx de entrega</label>
                                <input class="form-control" type="number" step="any" name="taxa_entrega" id="taxa_entrega"
                                       value="{{$pedido->taxa_entrega}}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="data_pedido">Data Pedido</label>
                        <input class="form-control" type="date" name="dataPedido" id="data_pedido"
                               value="{{$pedido->dataPedido ? $pedido->dataPedido->format('Y-m-d'):''}}">
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary" @click="adicionarItem()">Nova linha</button>
                        <table class="table">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Item</th>
                                <th>Valor</th>
                                <th>Quant</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item,key) in items">
                                <td>
                                    <button type="button" @click="removerLinha(key)">Remover</button>
                                </td>
                                <td>
                                    <v-select
                                            :disabled="items[key].valor!==null && items[key].valor!==undefined"
                                            v-model="items[key].selected"
                                            :options='{!! json_encode($items) !!}'
                                    ></v-select>
                                    <input type="hidden" :name="'items['+key+'][item]'">
                                </td>
                                <td v-cloak>
                                    {!! '[[ valor(items[key]) ]]' !!}
                                </td>
                                <td>
                                    <input
                                            :readonly="items[key].valor!==null && items[key].valor!==undefined"
                                            class="form-control"
                                            type="number"
                                            :name="'items['+key+'][quantidade]'"
                                            step="1"
                                            min="1"
                                            :value="item.quantidade"
                                    />
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue-select/2.4.0/vue-select.js"></script>
    <script>
        Vue.component('v-select', VueSelect.VueSelect);
        var app = new Vue({
            delimiters: ['[[', ']]'],
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
                },
                valor(item){
                    console.log(item)
                    if(item.valor){
                        return 'R$ '+item.valor;
                    }

                    return !item.selected?'': 'R$ '+item.selected.value.valor
                }
            }
        })
    </script>
@endpush
