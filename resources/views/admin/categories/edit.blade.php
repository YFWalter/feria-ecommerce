@extends('layouts.admin')
@section('title', 'Editar categoría')

@section('content')
<form action="{{ route('admin.categorias.update', $category) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.categories._form', ['submitLabel' => 'Guardar cambios'])
</form>
@endsection
