@extends('layouts.admin')
@section('title', 'Nueva categoría')

@section('content')
<form action="{{ route('admin.categorias.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('admin.categories._form', ['submitLabel' => 'Crear categoría'])
</form>
@endsection
