<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Title Meta -->
    <meta charset="utf-8" />
    <title>@yield('title') | HRIS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully responsive premium admin dashboard template" />
    <meta name="author" content="Techzaa" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Vendor CSS -->
    <link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Icons CSS -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- App CSS -->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Theme Config JS -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>

    <!-- Fullcalendar css -->
    <link href="{{ asset('assets/vendor/fullcalendar/main.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Custom Page CSS -->
    @stack('styles')


</head>

<body>

    @include('layouts.navigation')

    <div class="page-content">

        @include('layouts.topbar')

        <main class="content">

            @yield('content')

        </main>

        @include('layouts.footer')

    </div>

    <script src="{{ asset('assets/js/vendor.js') }}"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    @stack('scripts')

</body>

</html>