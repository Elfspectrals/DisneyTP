<x-app-layout>
    <div class="min-h-screen bg-[#1a1a1a]">
        @if(request('search'))
    {{-- MODE RECHERCHE --}}
    <div class="max-w-7xl mx-auto px-8 py-12">
        <h2 class="text-3xl font-bold text-white mb-8">
            Résultats pour « {{ request('search') }} »
        </h2>

        {{-- Films --}}
        @if($movies->count())
            <h3 class="text-2xl font-semibold text-white mb-4">Films</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6 mb-12">
                @foreach($movies as $movie)
                    <a href="{{ route('content.show', ['slug' => $movie->slug]) }}">
                        <img src="{{ $movie->poster_url ?? $movie->poster }}"
                             alt="{{ $movie->title }}"
                             class="rounded hover:scale-105 transition">
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Séries --}}
        @if($series->count())
            <h3 class="text-2xl font-semibold text-white mb-4">Séries</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @foreach($series as $serie)
                    <a href="{{ route('content.show', ['slug' => $serie->slug]) }}">
                        <img src="{{ $serie->poster_url ?? $serie->poster }}"
                             alt="{{ $serie->title }}"
                             class="rounded hover:scale-105 transition">
                    </a>
                @endforeach
            </div>
        @endif

        @if(!$movies->count() && !$series->count())
            <p class="text-gray-400">Aucun résultat trouvé.</p>
        @endif
    </div>

@else

        <!-- Hero Banner Section -->
        @if($featuredMovies->first() || $featuredSeries->first())
        <div class="relative h-[60vh] md:h-[70vh] overflow-hidden">
            @php
                $heroContent = $featuredMovies->first() ?? $featuredSeries->first();
            @endphp
            @if($heroContent->backdrop)
            <div class="absolute inset-0">
                <img src="{{ $heroContent->backdrop_url ?? $heroContent->backdrop }}" alt="{{ $heroContent->title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-[#1a1a1a] via-[#1a1a1a]/80 to-transparent"></div>
            </div>
            @else
            <div class="absolute inset-0 bg-gradient-to-br from-[#0063e5] to-[#764ba2]"></div>
            @endif

            <div class="relative z-10 h-full flex items-center">
                <div class="max-w-7xl mx-auto px-8 w-full">
                    <div class="max-w-2xl">
                        <div class="mb-4">
                            <span class="inline-block bg-black/50 px-3 py-1 rounded text-sm font-semibold text-white">
                                @if($heroContent instanceof \App\Models\Movie)
                                    Nouveau film
                                @else
                                    Nouvelle série
                                @endif
                            </span>
                        </div>
                        <h1 class="text-5xl md:text-7xl font-bold mb-4 text-white leading-tight">
                            {{ strtoupper($heroContent->title) }}
                        </h1>
                        <div class="flex items-center gap-4 mb-6 text-white">
                            <span class="text-lg">{{ $heroContent->release_year }}</span>
                            @if($heroContent instanceof \App\Models\Movie)
                            <span class="text-lg">{{ $heroContent->duration }} min</span>
                            @else
                            <span class="text-lg">{{ $heroContent->seasons }} saisons</span>
                            @endif
                            @if($heroContent->genres->first())
                            <span class="text-lg">{{ $heroContent->genres->first()->name }}</span>
                            @endif
                        </div>
                        <p class="text-lg text-gray-300 mb-6 line-clamp-3">{{ $heroContent->description }}</p>
                        <div class="flex gap-4">
                            <a href="{{ route('content.show', ['slug' => $heroContent->slug]) }}" class="px-8 py-3 bg-white text-black font-semibold rounded hover:bg-gray-200 transition">
                                ▶ Regarder
                            </a>
                            @auth
                            @if(auth()->user()->profiles()->exists())
                            <form action="{{ route('watchlist.add', ['type' => $heroContent instanceof \App\Models\Movie ? 'movie' : 'series', 'id' => $heroContent->id]) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-8 py-3 bg-white/20 backdrop-blur-sm border border-white/30 text-white font-semibold rounded hover:bg-white/30 transition">
                                    + Ma liste
                                </button>
                            </form>
                            @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Content Sections -->
        <div class="max-w-7xl mx-auto px-8 py-8">
            @if($featuredMovies->count())
            <div class="mb-12">
                <h3 class="text-2xl font-bold text-white mb-4">Films en vedette</h3>
                <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
                    @foreach($featuredMovies as $movie)
                    <a href="{{ route('content.show', ['slug' => $movie->slug]) }}" class="group flex-shrink-0">
                        <div class="relative w-48 h-72 rounded overflow-hidden bg-gray-800">
                            @if($movie->poster)
                            <img src="{{ $movie->poster_url ?? $movie->poster }}" alt="{{ $movie->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-700 to-gray-800 text-gray-400 text-center p-4">
                                {{ $movie->title }}
                            </div>
                            @endif
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition"></div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($featuredSeries->count())
            <div class="mb-12">
                <h3 class="text-2xl font-bold text-white mb-4">Séries en vedette</h3>
                <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
                    @foreach($featuredSeries as $series)
                    <a href="{{ route('content.show', $series->slug) }}" class="group flex-shrink-0">
                        <div class="relative w-48 h-72 rounded overflow-hidden bg-gray-800">
                            @if($series->poster)
                            <img src="{{ $series->poster_url ?? $series->poster }}" alt="{{ $series->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-700 to-gray-800 text-gray-400 text-center p-4">
                                {{ $series->title }}
                            </div>
                            @endif
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition"></div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($latestMovies->count())
            <div class="mb-12">
                <h3 class="text-2xl font-bold text-white mb-4">Derniers films</h3>
                <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
                    @foreach($latestMovies as $movie)
                    <a href="{{ route('content.show', ['slug' => $movie->slug]) }}" class="group flex-shrink-0">
                        <div class="relative w-48 h-72 rounded overflow-hidden bg-gray-800">
                            @if($movie->poster)
                            <img src="{{ $movie->poster_url ?? $movie->poster }}" alt="{{ $movie->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-700 to-gray-800 text-gray-400 text-center p-4">
                                {{ $movie->title }}
                            </div>
                            @endif
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition"></div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($latestSeries->count())
            <div class="mb-12">
                <h3 class="text-2xl font-bold text-white mb-4">Dernières séries</h3>
                <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
                    @foreach($latestSeries as $series)
                    <a href="{{ route('content.show', $series->slug) }}" class="group flex-shrink-0">
                        <div class="relative w-48 h-72 rounded overflow-hidden bg-gray-800">
                            @if($series->poster)
                            <img src="{{ $series->poster_url ?? $series->poster }}" alt="{{ $series->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-700 to-gray-800 text-gray-400 text-center p-4">
                                {{ $series->title }}
                            </div>
                            @endif
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition"></div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
    @endif
</x-app-layout>
