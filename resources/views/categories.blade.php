@extends('layouts.app')

@section('title', 'Daftar Categories')

@section('content')
    <h1>Daftar Categories</h1>
    <table id="category-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Jurusan</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data akan dimuat di sini -->
        </tbody>
    </table>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('/api/category')
                .then(response => response.json())
                .then(data => {
                    console.log(data);  // Tampilkan data yang diterima dari API untuk debugging
                    const categoryTableBody = document.querySelector('#category-table tbody');
                    data.data.forEach(category => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${category.id}</td>
                            <td>${category.jurusan}</td>
                            <td>${category.created_at}</td>
                            <td>${category.updated_at}</td>
                        `;
                        categoryTableBody.appendChild(row);
                    });
                })
                .catch(error => console.error('Error fetching data:', error));
        });
    </script>
@endsection
