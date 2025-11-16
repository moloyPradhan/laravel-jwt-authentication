<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Default Title')</title>

    @include('layouts.headerLink')

</head>

<body>
    @include('layouts.header')

    <div class="container min-h-[calc(100vh-6rem)] mx-auto px-4 py-8">
        @yield('content')
    </div>

    @include('layouts.footer')

    @stack('scripts')

</body>

</html>
