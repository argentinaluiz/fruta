@extends('layouts.app')

@section('content')
    <div class="container" id="container-cliente">
        <div class="row">
            <h4>Editar cliente {{$cliente->nome}}</h4>
            <div class="col-md-12">
                <form action="{{route('clientes.update', ['cliente' => $cliente->id])}}" method="post">
                    {{csrf_field()}}
                    {{method_field('PUT')}}
                    <div class="form-group">
                        <label for="nome" class="form-l">Nome</label>
                        <textarea name="nome" class="form-control" id="nome" cols="30" rows="2" required>{{$cliente->nome}}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="perfil" class="form-l">Perfil</label>
                        <select class="form-control" class="form-control" name="perfil" id="perfil" required value="{{$cliente->perfil}}">
                            <option value="">Selecione o perfil</option>
                            @foreach($perfis as $perfil)
                                <option value="{{$perfil->id}}" {{$perfil->id == $cliente->perfilModel->id?'selected="selected"':''}}>{{$perfil->nome}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-l">E-mail</label>
                        <input type="email" class="form-control" name="email" id="email" value="{{$cliente->email}}"/>
                    </div>
                    <div class="form-group">
                        <label for="endereco" class="form-l">Endereço</label>
                        <textarea name="endereco" class="form-control" id="endereco" cols="30" rows="2">{{$cliente->endereco}}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="bairro" class="form-l">Bairro</label>
                        <textarea name="bairro" class="form-control" id="bairro" cols="30" rows="2">{{$cliente->bairro}}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="estado" class="form-l">Estado</label>
                        <select class="form-control" class="form-control" name="id_estado" id="estado" value="{{$cliente->cidade?$cliente->cidade->estado->id:''}}">
                            <option value="">Selecione o estado</option>
                            @foreach($estados as $estado)
                                <option value="{{$estado->id}}" {{$cliente->cidade && $estado->id == $cliente->cidade->estado->id? 'selected="selected"':''}}>{{$estado->sigla}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cidade" class="form-l">Cidade</label>
                        <select class="form-control" class="form-control" name="id_cidade" id="cidade" value="{{$cliente->id_cidade}}">
                            @if($cliente->cidade)
                                @foreach($cliente->cidade->estado->cidades as $cidade)
                                    <option value="{{$cidade->id}}" {{$cidade->id == $cliente->cidade->id? 'selected="selected"':''}}>{{$cidade->nome}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cep" class="form-l">CEP</label>
                        <textarea name="cep" class="form-control" id="cep" cols="30" rows="2">{{$cliente->cep}}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="telefone" class="form-l">Telefone</label>
                        <textarea name="telefone" class="form-control" id="telefone" cols="30" rows="2">{{$cliente->telefone}}</textarea>
                    </div>
                    <button class="btn btn-primary">Criar</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        $('#estado').change(function () {
            var idEstado = $(this).val();
            $.get('/get-cidades/' + idEstado, function (cidades) {
                $('#cidade').empty();
                $.each(cidades, function (key, value) {
                    $('#cidade').append("<option value=''>Selecione a cidade</option>");
                    $('#cidade').append('<option value=' + value.id + '>' + value.nome + '</option>');
                });
            });
        });
    </script>
@endpush
