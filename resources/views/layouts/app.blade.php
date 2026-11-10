<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ipms-blue': '#003c71',
                        'ipms-light-blue': '#009FDF',
                        'ipms-yellow': '#ffd100',
                    }
                }
            }
        }
    </script>

    <!-- Scripts - Fix untuk production -->
    @if(app()->environment('production'))
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFile = $manifest['resources/sass/app.scss']['file'] ?? null;
            $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
        @endphp
        @if($cssFile)
            <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
        @endif
        @if($jsFile)
            <script src="{{ asset('build/' . $jsFile) }}" defer></script>
        @endif
    @else
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @endif

    @yield('styles')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Custom Scrollbar */
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Reset styles */
        * {
            text-decoration: none !important;
            outline: none !important;
        }

        a, a:hover, a:focus, a:active, a:visited,
        button, button:hover, button:focus, button:active {
            text-decoration: none !important;
            outline: none !important;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-100">
    @auth
        @include('layouts.sidebar')
    @endauth

    <!-- Bootstrap JS & Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Alpine.js Tooltip Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('tooltip', {
                text: '',
                x: 0,
                y: 0,
                visible: false,
                show(text, el) {
                    const rect = el.getBoundingClientRect();
                    this.text = text;
                    this.x = rect.right + 12;
                    this.y = rect.top + rect.height / 2 + window.scrollY;
                    this.visible = true;
                },
                hide() {
                    this.visible = false;
                }
            });
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>