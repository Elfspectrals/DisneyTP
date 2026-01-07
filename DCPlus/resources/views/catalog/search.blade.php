@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8 text-white">

    <h1 class="text-2xl font-bold mb-6">
        Résultats pour « {{ $search }} »
    </h1>

    {{-- FILMS --}}
    <h2 class="text-xl font-semibold mb-4">Films</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
        @forelse ($movies as $movie)
            <div class="bg-[#1a1a1a] p-4 rounded">
                {{ $movie->title }}
            </div>
        @empty
            <p>Aucun film trouvé.</p>
        @endforelse
    </div>

    {{-- SÉRIES --}}
    <h2 class="text-xl font-semibold mb-4">Séries</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @forelse ($series as $serie)
            <div class="bg-[#1a1a1a] p-4 rounded">
                {{ $serie->title }}
            </div>
        @empty
            <p>Aucune série trouvée.</p>
        @endforelse
    </div>

</div>
@endsection
