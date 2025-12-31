<nav x-data="{ open: false }" class="bg-[#1a1a1a] border-b border-white/10">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center mr-8">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-white tracking-tight">
                        DCPlus
                    </a>
                </div>

                <!-- Navigation Links -->
                @auth
                <div class="hidden space-x-6 sm:flex items-center">
                    <a href="{{ route('catalog') }}" class="flex items-center gap-2 px-3 py-2 text-white hover:text-[#0063e5] transition {{ request()->routeIs('catalog') ? 'text-[#0063e5] border-b-2 border-[#0063e5]' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="font-medium">ACCUEIL</span>
                    </a>
                    <a href="{{ route('watchlist') }}" class="flex items-center gap-2 px-3 py-2 text-white hover:text-[#0063e5] transition {{ request()->routeIs('watchlist') ? 'text-[#0063e5] border-b-2 border-[#0063e5]' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="font-medium">MA LISTE</span>
                    </a>
                    <a href="{{ route('movies') }}" class="flex items-center gap-2 px-3 py-2 text-white hover:text-[#0063e5] transition {{ request()->routeIs('movies') ? 'text-[#0063e5] border-b-2 border-[#0063e5]' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <span class="font-medium">FILMS</span>
                    </a>
                    <a href="{{ route('series') }}" class="flex items-center gap-2 px-3 py-2 text-white hover:text-[#0063e5] transition {{ request()->routeIs('series') ? 'text-[#0063e5] border-b-2 border-[#0063e5]' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="font-medium">SÉRIES</span>
                    </a>
                    @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-white hover:text-[#0063e5] transition {{ request()->routeIs('admin.*') ? 'text-[#0063e5] border-b-2 border-[#0063e5]' : '' }}">
                        <span class="font-medium">ADMIN</span>
                    </a>
                    @endif
                </div>
                @endauth
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                <div class="flex items-center gap-4">
                    <a href="{{ route('profiles.index') }}" class="flex items-center gap-2 text-white hover:text-[#0063e5] transition">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="font-medium">{{ auth()->user()->name }}</span>
                    </a>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-[#1a1a1a] hover:text-[#0063e5] focus:outline-none transition ease-in-out duration-150">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
                @else
                <div class="flex gap-4">
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-[#1a1a1a] border border-white/20 hover:border-white/40 text-white rounded transition">S'IDENTIFIER</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-[#0063e5] hover:bg-[#0483ee] text-white rounded transition">S'INSCRIRE</a>
                </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#1a1a1a] border-t border-white/10">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('catalog') }}" class="block px-4 py-2 text-white hover:bg-white/10 transition">
                ACCUEIL
            </a>
            @auth
            <a href="{{ route('movies') }}" class="block px-4 py-2 text-white hover:bg-white/10 transition">
                FILMS
            </a>
            <a href="{{ route('series') }}" class="block px-4 py-2 text-white hover:bg-white/10 transition">
                SÉRIES
            </a>
            <a href="{{ route('watchlist') }}" class="block px-4 py-2 text-white hover:bg-white/10 transition">
                MA LISTE
            </a>
            <a href="{{ route('profiles.index') }}" class="block px-4 py-2 text-white hover:bg-white/10 transition">
                PROFILS
            </a>
            @if(auth()->user()->is_admin)
            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-white hover:bg-white/10 transition">
                ADMIN
            </a>
            @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
        <div class="pt-4 pb-1 border-t border-white/10">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @else
        <div class="pt-4 pb-1 border-t border-white/10">
            <div class="px-4 space-y-2">
                <a href="{{ route('login') }}" class="block px-4 py-2 bg-[#1a1a1a] border border-white/20 text-white rounded text-center">S'IDENTIFIER</a>
                <a href="{{ route('register') }}" class="block px-4 py-2 bg-[#0063e5] hover:bg-[#0483ee] text-white rounded text-center transition">S'INSCRIRE</a>
            </div>
        </div>
        @endauth
    </div>
</nav>
