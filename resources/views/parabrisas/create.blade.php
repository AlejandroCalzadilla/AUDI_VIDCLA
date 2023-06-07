@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Nuevo VEHICULO</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            {!! Form::open(['route' => 'admin.parabrisa.store']) !!}

            <div class="form-group">
                {!! Form::label('precio', 'Precio: ') !!}
                {!! Form::number('precio', null, [
                    'class' => 'form-control' . ($errors->has('precio') ? ' is-invalid' : ''),
                    'placeholder' => 'Escriba el precio...',
                ]) !!}

                @error('precio')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('abajo', 'Abajo: ') !!}
                {!! Form::text('abajo', null, [
                    'class' => 'form-control' . ($errors->has('abajo') ? ' is-invalid' : ''),
                    'placeholder' => 'Escriba la medida de abajo...',
                ]) !!}

                @error('abajo')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('arriba', 'Arriba: ') !!}
                {!! Form::text('arriba', null, [
                    'class' => 'form-control' . ($errors->has('arriba') ? ' is-invalid' : ''),
                    'placeholder' => 'Escriba la medida de arriba...',
                ]) !!}

                @error('arriba')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('costado', 'Costado: ') !!}
                {!! Form::text('costado', null, [
                    'class' => 'form-control' . ($errors->has('costado') ? ' is-invalid' : ''),
                    'placeholder' => 'Escriba la medida del costado...',
                ]) !!}

                @error('costado')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('medio', 'Medio: ') !!}
                {!! Form::text('medio', null, [
                    'class' => 'form-control' . ($errors->has('medio') ? ' is-invalid' : ''),
                    'placeholder' => 'Escriba la medida del medio...',
                ]) !!}

                @error('medio')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('observacion', 'Observación: ') !!}
                {!! Form::textarea('observacion', null, [
                    'class' => 'form-control' . ($errors->has('observacion') ? ' is-invalid' : ''),
                    'placeholder' => 'Escriba la observación...',
                ]) !!}

                @error('observacion')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('posicion_id', 'Posición: ') !!}
                {!! Form::select('posicion_id', $posiciones->pluck('nombre', 'id'), null, [
                    'class' => 'form-control' . ($errors->has('posicion_id') ? ' is-invalid' : ''),
                    'placeholder' => 'Seleccione una posición...',
                ]) !!}

                @error('posicion_id')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('categoria_id', 'Categoría: ') !!}
                {!! Form::select('categoria_id', $categorias->pluck('nombre', 'id'), null, [
                    'class' => 'form-control' . ($errors->has('categoria_id') ? ' is-invalid' : ''),
                    'placeholder' => 'Seleccione una categoría...',
                ]) !!}

                @error('categoria_id')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('vehiculo_id', 'Vehículo: ') !!}
                {!! Form::select('vehiculo_id', $vehiculos->pluck('matricula', 'id'), null, [
                    'class' => 'form-control' . ($errors->has('vehiculo_id') ? ' is-invalid' : ''),
                    'placeholder' => 'Seleccione un vehículo...',
                ]) !!}

                @error('vehiculo_id')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {!! Form::submit('Crear PARABRISA', ['class' => 'btn btn-primary mt-2']) !!}

            {!! Form::close() !!}
        </div>
    </div>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Hi!');
    </script>
@stop
