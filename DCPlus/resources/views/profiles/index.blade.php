<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Qui est-ce ?') }}
            </h2>
            <a href="{{ route('profiles.create') }}" class="px-4 py-2 bg-[#1a1a1a] border border-white/20 hover:border-white/40 text-white rounded transition">
                MODIFIER LES PROFILS
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-[#1a1a1a] flex items-center justify-center py-12">
        <div class="max-w-6xl mx-auto px-8 text-center">
            <h1 class="text-6xl md:text-7xl font-bold mb-12 text-white">Qui est-ce ?</h1>
            
            @if($profiles->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6 mb-8">
                @foreach($profiles as $profile)
                <a href="{{ route('profiles.switch', $profile) }}" class="group">
                    <div class="flex flex-col items-center">
                        <div class="relative mb-3">
                            <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-4xl md:text-5xl font-bold text-white shadow-lg group-hover:scale-110 transition-transform duration-300 border-4 border-transparent group-hover:border-white">
                                {{ strtoupper(substr($profile->name, 0, 1)) }}
                            </div>
                            @if($profile->is_kid)
                            <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2">
                                <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 01.515 1.606l-4.462 2.908 1.214 4.83a1 1 0 01-1.45 1.137l-4.21-2.512-4.21 2.512a1 1 0 01-1.45-1.137l1.214-4.83L2.985 10.74a1 1 0 01.515-1.606L6.854 7.2 7.033 2.744A1 1 0 018 2z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            @endif
                        </div>
                        <p class="text-white text-lg font-medium group-hover:text-[#0063e5] transition">{{ $profile->name }}</p>
                    </div>
                </a>
                @endforeach
                
                @if($profiles->count() < 5)
                <a href="{{ route('profiles.create') }}" class="group">
                    <div class="flex flex-col items-center">
                        <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-white/10 border-2 border-dashed border-white/30 flex items-center justify-center group-hover:border-white/50 group-hover:bg-white/20 transition">
                            <svg class="w-12 h-12 text-white/50 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <p class="text-white/50 text-lg font-medium mt-3 group-hover:text-white transition">Ajouter</p>
                    </div>
                </a>
                @endif
            </div>
            @else
            <div class="mb-8">
                <p class="text-gray-400 mb-6">Aucun profil. Créez votre premier profil pour commencer.</p>
                <a href="{{ route('profiles.create') }}" class="inline-block px-6 py-3 bg-[#0063e5] hover:bg-[#0483ee] text-white font-semibold rounded transition">
                    Créer un profil
                </a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
