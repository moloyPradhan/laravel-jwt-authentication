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

    @include('layouts.sellerSidebar')

    {{-- content is in sidebar --}}

    @include('layouts.footer')

    @stack('scripts')

</body>

</html>
