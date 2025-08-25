<x-guest-layout>
    {{-- Kita akan menimpa styling default dengan desain baru ini --}}
    {{-- Menambahkan Font, Ikon, dan CSS Kustom --}}
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Apotek Parakan Muncang</title>
        
        <!-- Tailwind CSS (biasanya sudah ada di app.css, tapi ditambahkan untuk standalone) -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Google Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Feather Icons for input icons -->
        <script src="https://unpkg.com/feather-icons"></script>

        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            /* Menyesuaikan input Jetstream agar ada ruang untuk ikon */
            input[type="email"], input[type="password"] {
                padding-left: 2.5rem !important;
            }
        </style>
    </head>

    <body class="bg-gray-100">
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
            <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl flex overflow-hidden">
                
                <!-- Kolom Kiri: Branding & Informasi -->
                <div class="hidden md:flex flex-col justify-between w-1/2 bg-gradient-to-br from-blue-600 to-indigo-700 p-8 text-white">
                    <div>
                        <div class="flex items-center space-x-3">
                            {{-- Menggunakan logo dari komponen Jetstream --}}
                            <div class="bg-white p-2 rounded-lg">
                                <x-authentication-card-logo class="w-8 h-8" />
                            </div>
                            <h1 class="text-2xl font-bold">Apotek Parakan Muncang</h1>
                        </div>
                        <p class="mt-4 text-blue-200">Sistem Manajemen Apotek. Silakan login untuk melanjutkan.</p>
                    </div>
                    <div class="text-sm text-blue-300">
                        &copy; {{ date('Y') }} Apotek Parakan Muncang. All rights reserved.
                    </div>
                </div>

                <!-- Kolom Kanan: Form Login -->
                <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
                    <div class="md:hidden text-center mb-8">
                        <x-authentication-card-logo class="mx-auto w-20 h-20" />
                        <h1 class="text-2xl font-bold text-gray-800 mt-4">Apotek Parakan Muncang</h1>
                    </div>

                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang</h2>
                    <p class="text-gray-500 mb-8">Masukkan akun Anda untuk mengakses sistem.</p>

                    {{-- Menampilkan error validasi dari Jetstream --}}
                    <x-validation-errors class="mb-4" />

                    {{-- Menampilkan status session dari Jetstream --}}
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <!-- Input Email -->
                        <div class="mb-5">
                            <x-label for="email" value="{{ __('Email') }}" class="block mb-2 text-sm font-medium text-gray-600" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-feather="mail" class="w-5 h-5 text-gray-400"></i>
                                </div>
                                <x-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="contoh@email.com" />
                            </div>
                        </div>

                        <!-- Input Password -->
                        <div class="mb-6">
                            <x-label for="password" value="{{ __('Password') }}" class="block mb-2 text-sm font-medium text-gray-600" />
                             <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-feather="lock" class="w-5 h-5 text-gray-400"></i>
                                </div>
                                <x-input id="password" class="block w-full" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                            </div>
                        </div>

                        <!-- Tombol Login -->
                        <div>
                            <x-button class="w-full flex justify-center py-3 transition-colors duration-300">
                                {{ __('Log in') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            // Inisialisasi Feather Icons
            feather.replace();
        </script>
    </body>
</x-guest-layout>
