<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} — GoCarbu Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('css/style.css') }}">
    @stack('csss')
</head>

<body>

    @include('layouts.menu')

    <div class="main-wrapper">

        @include('layouts.header')

        @yield('content')
    </div>

    <!-- ── Toast container ── -->
    <div class="toast-container"></div>

    @include('layouts.scripts')

    @stack('scripts')
</body>

</html>
