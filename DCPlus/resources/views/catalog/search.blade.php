<x-app-layout>
    <div class="min-h-screen bg-[#1a1a1a]">
        <div class="max-w-7xl mx-auto px-8 py-12">
            <h2 class="text-3xl font-bold text-white mb-8">
                Résultats pour « {{ $search }} »
            </h2>

            {{-- Résultats Locaux (Films) --}}
            @if($movies->count())
                <h3 class="text-2xl font-semibold text-white mb-4">Films (Local)</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6 mb-12">
                    @foreach($movies as $movie)
                        <a href="{{ route('content.show', ['slug' => $movie->slug]) }}" class="group">
                            <div class="relative aspect-[2/3] rounded overflow-hidden bg-gray-800">
                                @if($movie->poster)
                                    <img src="{{ Str::startsWith($movie->poster, 'http') ? $movie->poster : asset('storage/' . $movie->poster) }}"
                                        alt="{{ $movie->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-500 text-center p-2">
                                        {{ $movie->title }}
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Résultats Locaux (Séries) --}}
            @if($series->count())
                <h3 class="text-2xl font-semibold text-white mb-4">Séries (Local)</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6 mb-12">
                    @foreach($series as $serie)
                        <a href="{{ route('content.show', ['slug' => $serie->slug]) }}" class="group">
                            <div class="relative aspect-[2/3] rounded overflow-hidden bg-gray-800">
                                @if($serie->poster)
                                    <img src="{{ Str::startsWith($serie->poster, 'http') ? $serie->poster : asset('storage/' . $serie->poster) }}"
                                        alt="{{ $serie->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-500 text-center p-2">
                                        {{ $serie->title }}
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Résultats Externes (TMDB) --}}
            @if($externalResults->count())
                <h3 class="text-2xl font-semibold text-white mb-4">Nouveaux ajouts (via TMDB)</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    @foreach($externalResults as $movie)
                        <a href="{{ route('content.show', ['slug' => $movie->slug]) }}" class="group">
                            <div class="relative aspect-[2/3] rounded overflow-hidden bg-gray-800">
                                @if($movie->poster)
                                    <img src="{{ $movie->poster_url }}"
                                        alt="{{ $movie->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-500 text-center p-2 italic text-sm">
                                        {{ $movie->title }}
                                    </div>
                                @endif
                                <div class="absolute bottom-0 left-0 right-0 p-2 bg-black/60 opacity-0 group-hover:opacity-100 transition">
                                    <p class="text-white text-xs truncate">{{ $movie->title }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            @if(!$movies->count() && !$series->count() && !count($externalResults))
                <p class="text-gray-400">Aucun résultat trouvé dans la base ni sur TMDB.</p>
            @endif
        </div>
    </div>
</x-app-layout>
