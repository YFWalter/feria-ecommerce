@extends('layouts.admin')
@section('title', 'Editar producto')

@section('content')
<form action="{{ route('admin.productos.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.products._form', ['submitLabel' => 'Guardar cambios'])
</form>
@endsection
