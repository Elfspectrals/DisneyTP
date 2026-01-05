<x-app-layout>
    <div class="min-h-screen bg-[#1a1a1a]">
        <div class="max-w-4xl mx-auto px-8 py-12">
            <h1 class="text-4xl font-bold text-white mb-8">Modifier le réalisateur</h1>
            
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-8">
                @if ($errors->any())
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-lg">
                    <h3 class="text-red-400 font-semibold mb-2">Erreurs de validation :</h3>
                    <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <form method="POST" action="{{ route('admin.directors.update', $director) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Nom *</label>
                        <input type="text" name="name" value="{{ old('name', $director->name) }}" required 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400">
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Photo</label>
                        @if($director->photo)
                        <div class="mb-3">
                            <img src="{{ Storage::url($director->photo) }}" alt="{{ $director->name }}" class="w-32 h-32 object-cover rounded-lg border border-white/20">
                            <p class="text-gray-400 text-sm mt-2">Photo actuelle</p>
                        </div>
                        @endif
                        <input type="file" name="photo" accept="image/*" 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#0063e5] file:text-white hover:file:bg-[#0483ee] file:cursor-pointer">
                        <p class="text-gray-400 text-sm mt-2">Formats acceptés : JPG, PNG, WebP (max 5MB). Laisser vide pour conserver la photo actuelle.</p>
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Date de naissance</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $director->birth_date ? $director->birth_date->format('Y-m-d') : '') }}" 
                            max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Biographie</label>
                        <textarea name="bio" rows="4" 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400">{{ old('bio', $director->bio) }}</textarea>
                    </div>
                    <div class="flex gap-4">
                        <button type="submit" class="px-6 py-3 bg-[#0063e5] hover:bg-[#0483ee] text-white font-semibold rounded-lg transition">
                            Mettre à jour
                        </button>
                        <a href="{{ route('admin.directors.index') }}" class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-lg font-semibold transition">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

