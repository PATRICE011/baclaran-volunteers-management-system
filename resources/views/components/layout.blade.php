<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Volunteers Management')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/baclaran-church-logo.jpg') }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Latest jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Toastr CSS for notifications -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <!-- Additional Styles (if any) -->
    @yield('styles')
</head>
<body>
    <!-- Content goes here -->
    @yield('content')

    <!-- Optional Scripts (if any) -->
    @yield('scripts')

    <!-- Toastr Notification Script -->
    <script>
        // Success Message (if available)
        @if(session('success'))
            toastr.success('{{ session('success') }}', 'Success', {
                positionClass: 'toast-top-right',
                timeOut: 3000, // auto-close after 3 seconds
            });
        @endif

        // Error Message (if available)
        @if(session('error'))
            toastr.error('{{ session('error') }}', 'Error', {
                positionClass: 'toast-top-right',
                timeOut: 3000, // auto-close after 3 seconds
            });
        @endif
    </script>
</body>
</html>
