<x-app-layout>
    <div class="min-h-screen bg-[#1a1a1a]">
        <div class="max-w-7xl mx-auto px-8 py-12">
            <h1 class="text-4xl font-bold text-white mb-8">Ma liste</h1>
            
            @if($watchlists->count() > 0)
            <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
                @foreach($watchlists as $watchlist)
                <div class="group flex-shrink-0">
                    <a href="{{ route('content.show', $watchlist->watchable->slug) }}" class="block">
                        <div class="relative w-48 h-72 rounded overflow-hidden bg-gray-800">
                            @if($watchlist->watchable->poster)
                            <img src="{{ $watchlist->watchable->poster }}" alt="{{ $watchlist->watchable->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-700 to-gray-800 text-gray-400 text-center p-4">
                                {{ $watchlist->watchable->title }}
                            </div>
                            @endif
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition flex items-center justify-center">
                                <div class="opacity-0 group-hover:opacity-100 transition">
                                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                    <form action="{{ route('watchlist.remove', ['type' => $watchlist->watchable instanceof \App\Models\Movie ? 'movie' : 'series', 'id' => $watchlist->watchable->id]) }}" method="POST" class="mt-3 text-center">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-gray-400 hover:text-red-400 text-sm transition">Retirer</button>
                    </form>
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-12 text-center">
                <svg class="w-24 h-24 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <p class="text-gray-400 text-lg mb-4">Votre liste est vide.</p>
                <a href="{{ route('catalog') }}" class="inline-block px-6 py-3 bg-[#0063e5] hover:bg-[#0483ee] text-white font-semibold rounded transition">
                    Parcourir le catalogue
                </a>
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
</x-app-layout>
