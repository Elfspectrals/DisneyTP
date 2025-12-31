<x-app-layout>
    <div class="min-h-screen bg-[#1a1a1a]">
        <!-- Hero Banner Section -->
        @if($featuredMovies->first())
        <div class="relative h-[60vh] md:h-[70vh] overflow-hidden">
            @php
                $heroContent = $featuredMovies->first();
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
                                Film en vedette
                            </span>
                        </div>
                        <h1 class="text-5xl md:text-7xl font-bold mb-4 text-white leading-tight">
                            {{ strtoupper($heroContent->title) }}
                        </h1>
                        <div class="flex items-center gap-4 mb-6 text-white">
                            <span class="text-lg">{{ $heroContent->release_year }}</span>
                            <span class="text-lg">{{ $heroContent->duration }} min</span>
                            @if($heroContent->genres->first())
                            <span class="text-lg">{{ $heroContent->genres->first()->name }}</span>
                            @endif
                            <span class="text-yellow-400">★ {{ $heroContent->rating }}</span>
                        </div>
                        <p class="text-lg text-gray-300 mb-6 line-clamp-3">{{ $heroContent->description }}</p>
                        <div class="flex gap-4">
                            <a href="{{ route('content.show', $heroContent->slug) }}" class="px-8 py-3 bg-white text-black font-semibold rounded hover:bg-gray-200 transition">
                                ▶ Regarder
                            </a>
                            @auth
                            @if(auth()->user()->profiles()->exists())
                            <form action="{{ route('watchlist.add', ['type' => 'movie', 'id' => $heroContent->id]) }}" method="POST" class="inline">
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

        <!-- Movies Grid Section -->
        <div class="max-w-7xl mx-auto px-8 py-8">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-white mb-2">Tous les films</h2>
                <p class="text-gray-400">{{ $movies->total() }} films disponibles</p>
            </div>

            @if($movies->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
                @foreach($movies as $movie)
                <a href="{{ route('content.show', $movie->slug) }}" class="group">
                    <div class="relative aspect-[2/3] rounded overflow-hidden bg-gray-800">
                        @if($movie->poster)
                        <img src="{{ $movie->poster_url ?? $movie->poster }}" alt="{{ $movie->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-700 to-gray-800 text-gray-400 text-center p-4 text-sm">
                            {{ $movie->title }}
                        </div>
                        @endif
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition"></div>
                        <div class="absolute top-2 right-2 bg-black/70 backdrop-blur-sm px-2 py-1 rounded text-white text-xs font-semibold">
                            ★ {{ $movie->rating }}
                        </div>
                    </div>
                    <div class="mt-2">
                        <h3 class="text-white font-medium text-sm line-clamp-1 group-hover:text-[#0063e5] transition">{{ $movie->title }}</h3>
                        <p class="text-gray-400 text-xs">{{ $movie->release_year }}</p>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $movies->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <p class="text-gray-400 text-lg">Aucun film disponible pour le moment.</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

