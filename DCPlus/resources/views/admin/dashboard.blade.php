<x-app-layout>
    <div class="min-h-screen bg-[#1a1a1a]">
        <div class="max-w-7xl mx-auto px-8 py-12">
            <h1 class="text-4xl font-bold text-white mb-8">Tableau de bord administrateur</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-6">
                    <h3 class="text-gray-400 mb-2 text-sm uppercase">Films</h3>
                    <p class="text-4xl font-bold text-white">{{ $stats['movies'] }}</p>
                </div>
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-6">
                    <h3 class="text-gray-400 mb-2 text-sm uppercase">Séries</h3>
                    <p class="text-4xl font-bold text-white">{{ $stats['series'] }}</p>
                </div>
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-6">
                    <h3 class="text-gray-400 mb-2 text-sm uppercase">Utilisateurs</h3>
                    <p class="text-4xl font-bold text-white">{{ $stats['users'] }}</p>
                </div>
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-6">
                    <h3 class="text-gray-400 mb-2 text-sm uppercase">Profils</h3>
                    <p class="text-4xl font-bold text-white">{{ $stats['profiles'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Actions rapides</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.movies.create') }}" class="block w-full bg-[#0063e5] hover:bg-[#0483ee] text-white px-4 py-3 rounded transition text-center font-medium">
                            Ajouter un film
                        </a>
                        <a href="{{ route('admin.series.create') }}" class="block w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded transition text-center font-medium">
                            Ajouter une série
                        </a>
                        <a href="{{ route('admin.movies.index') }}" class="block w-full bg-white/10 hover:bg-white/20 border border-white/20 text-white px-4 py-3 rounded transition text-center font-medium">
                            Gérer les films
                        </a>
                        <a href="{{ route('admin.series.index') }}" class="block w-full bg-white/10 hover:bg-white/20 border border-white/20 text-white px-4 py-3 rounded transition text-center font-medium">
                            Gérer les séries
                        </a>
                    </div>
                </div>

                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Contenu récent</h3>
                    <div class="space-y-3">
                        @foreach($recentMovies->take(3) as $movie)
                        <div class="flex justify-between items-center p-3 bg-white/5 rounded hover:bg-white/10 transition">
                            <span class="text-white">{{ $movie->title }}</span>
                            <a href="{{ route('admin.movies.edit', $movie) }}" class="text-[#0063e5] hover:text-[#0483ee] font-medium">Modifier</a>
                        </div>
                        @endforeach
                        @foreach($recentSeries->take(3) as $series)
                        <div class="flex justify-between items-center p-3 bg-white/5 rounded hover:bg-white/10 transition">
                            <span class="text-white">{{ $series->title }}</span>
                            <a href="{{ route('admin.series.edit', $series) }}" class="text-[#0063e5] hover:text-[#0483ee] font-medium">Modifier</a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
