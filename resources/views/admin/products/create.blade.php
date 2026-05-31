@extends('layouts.admin')
@section('title', 'Nuevo producto')

@section('content')
<form action="{{ route('admin.productos.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('admin.products._form', ['submitLabel' => 'Crear producto'])
</form>
@endsection
