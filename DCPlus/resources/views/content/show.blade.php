<x-app-layout>
    <div class="min-h-screen bg-[#1a1a1a]">
        <!-- Hero Banner -->
        <div class="relative h-[50vh] md:h-[60vh] overflow-hidden">
            @if($content->backdrop)
            <div class="absolute inset-0">
                <img src="{{ $content->backdrop }}" alt="{{ $content->title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-[#1a1a1a] via-[#1a1a1a]/90 to-transparent"></div>
            </div>
            @else
            <div class="absolute inset-0 bg-gradient-to-br from-[#0063e5] to-[#764ba2]"></div>
            @endif
            
            <div class="relative z-10 h-full flex items-end">
                <div class="max-w-7xl mx-auto px-8 w-full pb-12">
                    <div class="max-w-3xl">
                        <h1 class="text-4xl md:text-6xl font-bold mb-4 text-white leading-tight">
                            {{ $content->title }}
                        </h1>
                        <div class="flex items-center gap-4 mb-4 text-white">
                            <span class="text-lg">{{ $content->release_year }}</span>
                            @if($content instanceof \App\Models\Movie)
                            <span class="text-lg">{{ $content->duration }} min</span>
                            @else
                            <span class="text-lg">{{ $content->seasons }} saisons</span>
                            @endif
                            <span class="text-yellow-400">★ {{ $content->rating }}</span>
                        </div>
                        <div class="flex gap-4">
                            <a href="#" class="px-6 py-3 bg-white text-black font-semibold rounded hover:bg-gray-200 transition">
                                ▶ Regarder
                            </a>
                            @auth
                            @if(auth()->user()->profiles()->exists())
                            @if($isInWatchlist)
                            <form action="{{ route('watchlist.remove', ['type' => $content instanceof \App\Models\Movie ? 'movie' : 'series', 'id' => $content->id]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-6 py-3 bg-red-600/80 backdrop-blur-sm border border-red-500/50 text-white font-semibold rounded hover:bg-red-600 transition">
                                    ✗ Retirer de ma liste
                                </button>
                            </form>
                            @else
                            <form action="{{ route('watchlist.add', ['type' => $content instanceof \App\Models\Movie ? 'movie' : 'series', 'id' => $content->id]) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-6 py-3 bg-white/20 backdrop-blur-sm border border-white/30 text-white font-semibold rounded hover:bg-white/30 transition">
                                    + Ma liste
                                </button>
                            </form>
                            @endif
                            @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Details -->
        <div class="max-w-7xl mx-auto px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-1">
                    @if($content->poster)
                    <img src="{{ $content->poster }}" alt="{{ $content->title }}" class="w-full rounded-lg shadow-lg">
                    @endif
                </div>
                
                <div class="md:col-span-3">
                    <p class="text-lg text-gray-300 mb-6 leading-relaxed">{{ $content->description }}</p>

                    @if($content->genres->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-white mb-3">Genres</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($content->genres as $genre)
                            <span class="px-3 py-1 bg-white/10 rounded-full text-white text-sm">{{ $genre->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($content->actors->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-white mb-3">Distribution</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($content->actors as $actor)
                            <span class="text-gray-300">{{ $actor->name }}</span>@if(!$loop->last),@endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($content instanceof \App\Models\Series && $content->episodes->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Épisodes</h3>
                        <div class="space-y-3">
                            @foreach($content->episodes->groupBy('season_number') as $season => $episodes)
                            <div class="mb-6">
                                <h4 class="text-md font-semibold text-white mb-3">Saison {{ $season }}</h4>
                                <div class="space-y-2">
                                    @foreach($episodes as $episode)
                                    <div class="bg-white/5 p-4 rounded hover:bg-white/10 transition cursor-pointer">
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center gap-4">
                                                <span class="text-gray-400 text-sm">E{{ $episode->episode_number }}</span>
                                                <span class="text-white font-medium">{{ $episode->title }}</span>
                                            </div>
                                            <span class="text-gray-400 text-sm">{{ $episode->duration }} min</span>
                                        </div>
                                        @if($episode->description)
                                        <p class="text-gray-400 text-sm mt-2">{{ Str::limit($episode->description, 100) }}</p>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @auth
                    @if(auth()->user()->profiles()->exists())
                    <div class="mb-6 p-6 bg-white/5 rounded-lg">
                        <h3 class="text-lg font-semibold text-white mb-4">Notez et commentez</h3>
                        <form action="{{ route('content.rate', $content->slug) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="flex items-center gap-3">
                                <label class="text-white">Votre note:</label>
                                <select name="rating" class="bg-white/10 border border-white/20 text-white rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0063e5]">
                                    <option value="1">1 ⭐</option>
                                    <option value="2">2 ⭐</option>
                                    <option value="3">3 ⭐</option>
                                    <option value="4">4 ⭐</option>
                                    <option value="5" selected>5 ⭐</option>
                                </select>
                                <button type="submit" class="px-4 py-2 bg-[#0063e5] hover:bg-[#0483ee] text-white rounded transition">Noter</button>
                            </div>
                        </form>
                        <form action="{{ route('content.review', $content->slug) }}" method="POST">
                            @csrf
                            <textarea name="comment" rows="3" class="w-full bg-white/10 border border-white/20 text-white rounded p-3 mb-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] placeholder-gray-400" placeholder="Écrivez votre avis..."></textarea>
                            <button type="submit" class="px-4 py-2 bg-[#0063e5] hover:bg-[#0483ee] text-white rounded transition">Publier l'avis</button>
                        </form>
                    </div>
                    @endif
                    @endauth

                    @if($content->reviews->count() > 0)
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-4">Avis des spectateurs</h3>
                        <div class="space-y-4">
                            @foreach($content->reviews as $review)
                            <div class="bg-white/5 p-4 rounded-lg">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($review->profile->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-white font-medium">{{ $review->profile->name }}</p>
                                        <p class="text-gray-400 text-sm">{{ $review->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <p class="text-gray-300">{{ $review->comment }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
