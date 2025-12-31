<x-app-layout>
    <div class="min-h-screen bg-[#1a1a1a]">
        <div class="max-w-4xl mx-auto px-8 py-12">
            <h1 class="text-4xl font-bold text-white mb-8">Modifier le film</h1>
            
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-8">
                <form method="POST" action="{{ route('admin.movies.update', $movie) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-white mb-2 font-semibold">Titre *</label>
                            <input type="text" name="title" value="{{ old('title', $movie->title) }}" required 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400">
                        </div>
                        <div>
                            <label class="block text-white mb-2 font-semibold">Année de sortie *</label>
                            <input type="date" name="release_year" value="{{ old('release_year', $movie->release_year ? date('Y-m-d', strtotime($movie->release_year . '-01-01')) : '') }}" required 
                                min="1900-01-01" max="{{ date('Y-m-d') }}"
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Description *</label>
                        <textarea name="description" rows="4" required 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400">{{ old('description', $movie->description) }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-white mb-2 font-semibold">Durée (minutes) *</label>
                            <input type="number" name="duration" value="{{ old('duration', $movie->duration) }}" required 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-white mb-2 font-semibold">Affiche (image)</label>
                            @if($movie->poster)
                            <div class="mb-2">
                                <img src="{{ Storage::url($movie->poster) }}" alt="Affiche actuelle" class="w-32 h-48 object-cover rounded mb-2">
                                <p class="text-gray-400 text-sm">Affiche actuelle</p>
                            </div>
                            @endif
                            <input type="file" name="poster" accept="image/*" 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#0063e5] file:text-white hover:file:bg-[#0483ee] file:cursor-pointer">
                            <p class="text-gray-400 text-sm mt-2">Laisser vide pour conserver l'image actuelle</p>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Image de fond (backdrop)</label>
                        @if($movie->backdrop)
                        <div class="mb-2">
                            <img src="{{ Storage::url($movie->backdrop) }}" alt="Backdrop actuel" class="w-full max-w-md h-32 object-cover rounded mb-2">
                            <p class="text-gray-400 text-sm">Backdrop actuel</p>
                        </div>
                        @endif
                        <input type="file" name="backdrop" accept="image/*" 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#0063e5] file:text-white hover:file:bg-[#0483ee] file:cursor-pointer">
                        <p class="text-gray-400 text-sm mt-2">Laisser vide pour conserver l'image actuelle</p>
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Vidéo</label>
                        @if($movie->video_url)
                        <div class="mb-2">
                            <p class="text-gray-400 text-sm mb-2">Vidéo actuelle : {{ basename($movie->video_url) }}</p>
                            <video src="{{ Storage::url($movie->video_url) }}" controls class="w-full max-w-md rounded mb-2"></video>
                        </div>
                        @endif
                        <input type="file" name="video_url" accept="video/*" 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#0063e5] file:text-white hover:file:bg-[#0483ee] file:cursor-pointer">
                        <p class="text-gray-400 text-sm mt-2">Laisser vide pour conserver la vidéo actuelle</p>
                    </div>
                    <div class="mb-6">
                        <label class="flex items-center text-white cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $movie->is_featured) ? 'checked' : '' }} 
                                class="mr-3 w-5 h-5 rounded bg-white/10 border-white/20 text-[#0063e5] focus:ring-[#0063e5]">
                            <span class="font-semibold">Mettre en vedette</span>
                        </label>
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Genres</label>
                        <select name="genres[]" multiple 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                            @foreach($genres as $genre)
                            <option value="{{ $genre->id }}" {{ $movie->genres->contains($genre->id) ? 'selected' : '' }}>{{ $genre->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-gray-400 text-sm mt-2">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs genres</p>
                    </div>
                    <div class="flex gap-4">
                        <button type="submit" class="px-6 py-3 bg-[#0063e5] hover:bg-[#0483ee] text-white font-semibold rounded-lg transition">
                            Mettre à jour
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
