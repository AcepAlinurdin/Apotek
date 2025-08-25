<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <h1 class="text-lg font-bold text-gray-800">Apotek Parakan Muncang</h1>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                    @hasanyrole('kepala apotek')
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @endhasanyrole

                    @hasanyrole('admin|kepala apotek')
                    <x-nav-link href="{{ route('obat.master.index') }}" :active="request()->routeIs('obat.master.*')">
                        {{ __('Master Data Obat') }}
                    </x-nav-link>
                    @endhasanyrole

                    <x-nav-link href="{{ route('penjualan.index') }}" :active="request()->routeIs('penjualan.index')">
                        {{ __('Penjualan') }}
                    </x-nav-link>

                    <x-nav-link href="{{ route('obat.stok') }}" :active="request()->routeIs('obat.stok')">
                        {{ __('Pengecekan Stok') }}
                    </x-nav-link>

                    {{-- ====================================================== --}}
                    {{-- =========== PERUBAHAN UTAMA ADA DI SINI ============== --}}
                    {{-- ====================================================== --}}
                    @hasanyrole('apoteker|kepala apotek')
                    <x-nav-link href="{{ route('obat.rekap') }}" :active="request()->routeIs('obat.rekap')">
                        {{ __('Pembelian') }}
                    </x-nav-link>
                    @endhasanyrole
                    {{-- ====================================================== --}}

                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <span class="inline-flex rounded-md">
                            <button type="button"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                {{ Auth::user()->name }}
                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </span>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Account Management -->
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Manage Account') }}
                        </div>
                        <x-dropdown-link href="{{ route('profile.show') }}">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <div class="border-t border-gray-200"></div>
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf
                            <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @hasanyrole('admin|kepala apotek')
            <x-responsive-nav-link href="{{ route('obat.master.index') }}"
                :active="request()->routeIs('obat.master.*')">
                {{ __('Master Data Obat') }}
            </x-responsive-nav-link>
            @endhasanyrole

            <x-responsive-nav-link href="{{ route('penjualan.index') }}"
                :active="request()->routeIs('penjualan.index')">
                {{ __('Penjualan') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('obat.stok') }}" :active="request()->routeIs('obat.stok')">
                {{ __('Pengecekan Stok') }}
            </x-responsive-nav-link>
            
            @hasanyrole('apoteker|kepala apotek')
            <x-responsive-nav-link href="{{ route('obat.rekap') }}" :active="request()->routeIs('obat.rekap')">
                {{ __('Pembelian') }}
            </x-responsive-nav-link>
            @endhasanyrole
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                <div class="shrink-0 me-3">
                    <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                        alt="{{ Auth::user()->name }}" />
                </div>
                @endif
                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
