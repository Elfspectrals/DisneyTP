<x-app-layout>
    <div class="min-h-screen bg-[#1a1a1a]">
        <div class="max-w-7xl mx-auto px-8 py-12">
            <h1 class="text-4xl font-bold text-white mb-8">Gérer les épisodes : {{ $series->title }}</h1>
            
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-8 mb-8">
                <h3 class="text-xl font-semibold text-white mb-6">Ajouter un nouvel épisode</h3>
                <form method="POST" action="{{ route('admin.series.episodes.store', $series) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-white mb-2 font-semibold">Saison *</label>
                            <input type="number" name="season_number" value="{{ old('season_number', 1) }}" required 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-white mb-2 font-semibold">Épisode *</label>
                            <input type="number" name="episode_number" value="{{ old('episode_number', 1) }}" required 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-white mb-2 font-semibold">Durée (min) *</label>
                            <input type="number" name="duration" value="{{ old('duration') }}" required 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-white mb-2 font-semibold">Date de diffusion</label>
                            <input type="date" name="air_date" value="{{ old('air_date') }}" 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-white mb-2 font-semibold">Titre *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400">
                    </div>
                    <div class="mb-4">
                        <label class="block text-white mb-2 font-semibold">Description</label>
                        <textarea name="description" rows="2" 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-white mb-2 font-semibold">URL de la vidéo</label>
                        <input type="text" name="video_url" value="{{ old('video_url') }}" 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400">
                    </div>
                    <button type="submit" class="px-6 py-3 bg-[#0063e5] hover:bg-[#0483ee] text-white font-semibold rounded-lg transition">
                        Ajouter l'épisode
                    </button>
                </form>
            </div>

            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-8">
                <h3 class="text-xl font-semibold text-white mb-6">Épisodes</h3>
                <div class="space-y-6">
                    @foreach($episodes->groupBy('season_number') as $season => $seasonEpisodes)
                    <div>
                        <h4 class="text-lg font-semibold text-white mb-4">Saison {{ $season }}</h4>
                        <div class="space-y-2">
                            @foreach($seasonEpisodes as $episode)
                            <div class="bg-white/5 p-4 rounded-lg hover:bg-white/10 transition">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-4">
                                        <span class="text-gray-400 text-sm font-medium">S{{ $episode->season_number }}E{{ $episode->episode_number }}</span>
                                        <span class="text-white font-medium">{{ $episode->title }}</span>
                                        <span class="text-gray-400 text-sm">({{ $episode->duration }} min)</span>
                                    </div>
                                </div>
                                @if($episode->description)
                                <p class="text-gray-400 text-sm mt-2">{{ Str::limit($episode->description, 150) }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
