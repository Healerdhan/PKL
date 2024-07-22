@extends('layouts.app')

@section('title', 'Daftar Categories')

@section('content')
    <h1>Daftar Categories</h1>
    <table id="category-table" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Jurusan</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->jurusan }}</td>
                    <td>{{ $category->created_at }}</td>
                    <td>{{ $category->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
