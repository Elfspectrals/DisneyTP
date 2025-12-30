<x-app-layout>
    <div class="min-h-screen bg-[#1a1a1a]">
        <div class="max-w-7xl mx-auto px-8 py-12">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-bold text-white">Gérer les films</h1>
                <a href="{{ route('admin.movies.create') }}" class="px-6 py-3 bg-[#0063e5] hover:bg-[#0483ee] text-white font-semibold rounded-lg transition">
                    Ajouter un film
                </a>
            </div>

            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-white/5">
                            <tr>
                                <th class="text-left text-white py-4 px-6 font-semibold">Titre</th>
                                <th class="text-left text-white py-4 px-6 font-semibold">Année</th>
                                <th class="text-left text-white py-4 px-6 font-semibold">Note</th>
                                <th class="text-left text-white py-4 px-6 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movies as $movie)
                            <tr class="border-t border-white/10 hover:bg-white/5 transition">
                                <td class="text-white py-4 px-6">{{ $movie->title }}</td>
                                <td class="text-gray-400 py-4 px-6">{{ $movie->release_year }}</td>
                                <td class="text-yellow-400 py-4 px-6">★ {{ $movie->rating }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex gap-3">
                                        <a href="{{ route('admin.movies.edit', $movie) }}" class="text-[#0063e5] hover:text-[#0483ee] font-medium">Modifier</a>
                                        <form action="{{ route('admin.movies.destroy', $movie) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300 font-medium">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-white/10">
                    {{ $movies->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
