<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Meta -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('title')</title>

    <!-- vendor css -->
    <link href="{{ asset('assets/lib/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/typicons.font/typicons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

    <!-- azia CSS -->
    <link href="{{ asset('assets/css/azia.css') }}" rel="stylesheet">

</head>

<body class="az-body">

    @include('layouts.header')


    @yield('content')


    @include('layouts.footer')
    <script src="{{ asset('assets/lib/jquery/jquery.min.js') }}">
        < script src = "{{ asset('assets/lib/ionicons/ionicons.js') }}" >
    </script>
    <script src="{{ asset('assets/js/azia.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <script>
        new DataTable('#example');
    </script>
    <script>
        $(function() {
            'use strict'

        });
    </script>
</body>

</html>
