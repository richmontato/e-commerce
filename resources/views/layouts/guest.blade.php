<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TechMart') }}</title>

        <link rel="icon" type="image/svg+xml" href="{{ asset('images/cart-icon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-blue-50 via-white to-blue-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Logo with gradient background -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-4 rounded-2xl shadow-xl">
                    <a href="/">
                        <svg viewBox="0 0 20 20" class="w-16 h-16 fill-current text-white" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 1a1 1 0 0 0 0 2h1.22l.305 1.222a.997.997 0 0 0 .01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 0 0 0-2H6.414l1-1H14a1 1 0 0 0 .894-.553l3-6A1 1 0 0 0 17 3H6.28l-.31-1.243A1 1 0 0 0 5 1H3zm13 15.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zM6.5 18a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
                    </svg>
                </a>
            </div>

            <!-- Brand name -->
            <h1 class="mt-4 text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                TechMart
            </h1>
            <p class="text-gray-600 text-sm mt-1">Modern E-Commerce Platform</p>

            <!-- Form card with modern styling -->
            <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white shadow-2xl overflow-hidden rounded-2xl border border-blue-100">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <p class="mt-6 text-xs text-gray-500">
                © 2025 TechMart. All rights reserved.
            </p>
        </div>
    </body>
</html>
