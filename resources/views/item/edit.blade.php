@extends('layouts.app')

@section('content')
    <div class="container" id="container-item">
        <div class="row">
            <h4>Editar item #{{$item->id}}</h4>
            <div class="col-md-12">
                <form id="my-form" action="{{route('items.update', ['item' => $item->id])}}" method="post" @submit.prevent="onsubmit">
                    {{csrf_field()}}
                    {{method_field('PUT')}}
                    <div class="form-group">
                        <label for="item">Nome</label>
                        <input class="form-control" type="text" name="item" id="item" value="{{$item->item}}">
                    </div>
                    <div class="form-group">
                        <label for="categoria" class="form-l">Categoria</label>
                        <select class="form-control" name="categoria" id="categoria" required>
                            @foreach($categorias as $categoria)
                                <option value="{{$categoria->id}}" {{$item->idcategoria==$categoria->id?'selected="selected"': ''}}>{{$categoria->nome}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ingredientes">Ingredientes</label>
                        <textarea class="form-control" name="ingredientes" id="ingredientes">{{$item->ingredientes}}</textarea>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary" @click="adicionarItem()">Nova linha</button>
                        <table class="table">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Valor (Obrigatório)</th>
                                <th>Volume</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(valor,key) in valores">
                                <td>
                                    <button type="button" @click="removerLinha(key)">Remover</button>
                                </td>
                                <td>
                                    <input
                                            class="form-control"
                                            type="number"
                                            :name="'valores['+key+'][valor]'"
                                            :value="valor.valor"
                                            step="any"
                                            min="1"
                                    />
                                </td>
                                <td>
                                    <input
                                            class="form-control"
                                            type="number"
                                            :name="'valores['+key+'][volume]'"
                                            :value="valor.volume"
                                            step="1"
                                            min="1"
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
    <script>
        Vue.config.delimiters = ['[[', ']]'];
        var app = new Vue({
            el: '#container-item',
            data: {
                valores: {!! json_encode($item->itemValores) !!}
            },
            methods: {
                adicionarItem: function () {
                    this.valores.push([]);
                },
                removerLinha: function (index) {
                    this.valores.splice(index, 1);
                },
                onsubmit(){
                    $('#my-form')[0].submit();
                }
            }
        })
    </script>
@endpush
