<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            text-align: center;
        }
        #category-table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        #category-table th, #category-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        #category-table th {
            background-color: #f2f2f2;
        }
        nav {
            background-color: #333;
            overflow: hidden;
        }
        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }
        nav ul li {
            float: left;
        }
        nav ul li a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }
        nav ul li a:hover {
            background-color: #111;
        }
    </style>
</head>
<body>
    @include('layouts.navbar')
    <div class="container">
        @yield('content')
    </div>
    @yield('scripts')
</body>
</html>
