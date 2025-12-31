<x-app-layout>
    <div class="min-h-screen bg-[#1a1a1a]">
        <div class="max-w-4xl mx-auto px-8 py-12">
            <h1 class="text-4xl font-bold text-white mb-8">Créer un film</h1>
            
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-8">
                @if ($errors->any())
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-lg">
                    <h3 class="text-red-400 font-semibold mb-2">Erreurs de validation :</h3>
                    <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                @if (session('success'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500/50 rounded-lg text-green-300">
                    {{ session('success') }}
                </div>
                @endif
                
                <form method="POST" action="{{ route('admin.movies.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-white mb-2 font-semibold">Titre *</label>
                            <input type="text" name="title" value="{{ old('title') }}" required 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400">
                        </div>
                        <div>
                            <label class="block text-white mb-2 font-semibold">Année de sortie *</label>
                            <input type="number" name="release_year" value="{{ old('release_year') }}" required 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Description *</label>
                        <textarea name="description" rows="4" required 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400">{{ old('description') }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-white mb-2 font-semibold">Durée (minutes) *</label>
                            <input type="number" name="duration" value="{{ old('duration') }}" required 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-white mb-2 font-semibold">URL de l'affiche</label>
                            <input type="text" name="poster" value="{{ old('poster') }}" 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400">
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">URL de l'image de fond</label>
                        <input type="text" name="backdrop" value="{{ old('backdrop') }}" 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400">
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">URL de la vidéo</label>
                        <input type="text" name="video_url" value="{{ old('video_url') }}" 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400">
                    </div>
                    <div class="mb-6">
                        <label class="flex items-center text-white cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} 
                                class="mr-3 w-5 h-5 rounded bg-white/10 border-white/20 text-[#0063e5] focus:ring-[#0063e5]">
                            <span class="font-semibold">Mettre en vedette</span>
                        </label>
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Genres</label>
                        <select name="genres[]" multiple 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                            @foreach($genres as $genre)
                            <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-gray-400 text-sm mt-2">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs genres</p>
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Acteurs</label>
                        <select name="actors[]" multiple 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                            @foreach($actors as $actor)
                            <option value="{{ $actor->id }}">{{ $actor->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-gray-400 text-sm mt-2">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs acteurs</p>
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Réalisateurs</label>
                        <select name="directors[]" multiple 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                            @foreach($directors as $director)
                            <option value="{{ $director->id }}">{{ $director->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-gray-400 text-sm mt-2">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs réalisateurs</p>
                    </div>
                    <div class="flex gap-4">
                        <button type="submit" class="px-6 py-3 bg-[#0063e5] hover:bg-[#0483ee] text-white font-semibold rounded-lg transition">
                            Créer le film
                        </button>
                        <a href="{{ route('admin.movies.index') }}" class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-lg font-semibold transition">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
