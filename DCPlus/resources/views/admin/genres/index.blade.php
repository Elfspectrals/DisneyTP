@php
use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <div class="min-h-screen bg-[#1a1a1a]">
        <div class="max-w-7xl mx-auto px-8 py-12">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-bold text-white">Gérer les genres</h1>
                <a href="{{ route('admin.genres.create') }}" class="px-6 py-3 bg-[#0063e5] hover:bg-[#0483ee] text-white font-semibold rounded-lg transition">
                    Ajouter un genre
                </a>
            </div>

            @if (session('success'))
            <div class="mb-6 p-4 bg-green-500/20 border border-green-500/50 rounded-lg text-green-300">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-white/5">
                            <tr>
                                <th class="text-left text-white py-4 px-6 font-semibold">Nom</th>
                                <th class="text-left text-white py-4 px-6 font-semibold">Description</th>
                                <th class="text-left text-white py-4 px-6 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($genres as $genre)
                            <tr class="border-t border-white/10 hover:bg-white/5 transition">
                                <td class="text-white py-4 px-6 font-medium">{{ $genre->name }}</td>
                                <td class="text-gray-400 py-4 px-6">{{ Str::limit($genre->description ?? 'Aucune description', 50) }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex gap-3">
                                        <a href="{{ route('admin.genres.edit', $genre) }}" class="text-[#0063e5] hover:text-[#0483ee] font-medium">Modifier</a>
                                        <form action="{{ route('admin.genres.destroy', $genre) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce genre ?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300 font-medium">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-gray-400 py-8">Aucun genre trouvé</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-white/10">
                    {{ $genres->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

