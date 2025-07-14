<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Volunteers Management')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/baclaran-church-logo.jpg') }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <!-- Boxicons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">

    <!-- Additional Styles -->
    @yield('styles')
</head>

<body>
    <style>
        body {
            font-family: "Montserrat", sans-serif;
        }
    </style>
    <!-- Page Content -->
    @yield('content')

    <!-- Toastr Notifications for Session -->
    @if(session('success'))
    <script>
        toastr.success(
            '{{ session('success') }}','Success', {
                positionClass: 'toast-top-right',
                timeOut: 3000
            }
        );
    </script>
    @endif

    @if(session('error'))
    <script>
        toastr.error(
            '{{ session('error') }}','Error', {
                positionClass: 'toast-top-right',
                timeOut: 3000
            }
        );
    </script>
    @endif

    <!-- Page Scripts -->
    @yield('scripts')

    {{-- Alpine.js CDN --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>

</html>