<x-app-layout>
    <div class="min-h-screen bg-[#1a1a1a] flex items-center justify-center py-12">
        <div class="max-w-md mx-auto px-8 w-full">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Créer un profil</h1>
                <p class="text-gray-400">Ajoutez un profil pour un membre de votre famille.</p>
            </div>

            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-8">
                @if(session('info'))
                <div class="mb-6 p-4 bg-blue-900/50 border border-blue-500 rounded text-blue-200">
                    {{ session('info') }}
                </div>
                @endif

                <form method="POST" action="{{ route('profiles.store') }}">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-white mb-3 text-lg font-semibold">Nom du profil</label>
                        <input type="text" name="name" value="{{ old('name') }}" required 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400"
                            placeholder="Entrez un nom">
                        @error('name')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-8">
                        <label class="flex items-center text-white cursor-pointer">
                            <input type="checkbox" name="is_kid" value="1" class="mr-3 w-5 h-5 rounded bg-white/10 border-white/20 text-[#0063e5] focus:ring-[#0063e5]">
                            <span class="text-lg">Profil Enfant</span>
                        </label>
                        <p class="text-sm text-gray-400 mt-2 ml-8">Les profils enfants ont un accès limité au contenu.</p>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 bg-[#0063e5] hover:bg-[#0483ee] text-white px-6 py-3 rounded-lg font-semibold transition">
                            Créer
                        </button>
                        <a href="{{ route('profiles.index') }}" class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-lg font-semibold transition">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
