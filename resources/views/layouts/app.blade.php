<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Default Title')</title>

    @include('layouts.headerLink')

</head>

<body>

    @php
        $isCartPage = request()->routeIs('cartPage');
    @endphp

    @if (!$isCartPage)
        @include('layouts.header')
    @endif


    <div class="container min-h-[calc(100vh-6rem)] mx-auto px-4 py-8">
        @yield('content')
        @if (!$isCartPage)
            <a id="floatingCart" href="{{ route('cartPage') }}"
                class="fixed-btn bg-gray-800 text-white w-12 h-12 rounded-full shadow-lg flex items-center justify-center cursor-pointer hover:bg-gray-900 transition">
                <div class="relative flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437m0 0L6.75 14.25m-1.644-8.978h12.276c.938 0 1.636.88 1.42 1.792l-1.2 5.013a1.5 1.5 0 01-1.46 1.17H7.012m0 0L6.75 17.25m.262-2.988H18m-11.25 0A1.5 1.5 0 105.25 18a1.5 1.5 0 001.5-1.5zm10.5 0A1.5 1.5 0 1015.75 18a1.5 1.5 0 001.5-1.5z" />
                    </svg>

                    <span id="cartCount"
                        class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full shadow">
                        0
                    </span>
                </div>
            </a>
        @endif

    </div>

    @if (!$isCartPage)
        @include('layouts.footer')
    @endif

    @stack('scripts')

</body>

</html>
