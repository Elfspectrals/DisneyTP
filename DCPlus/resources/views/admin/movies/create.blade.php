<x-app-layout>
    <div class="min-h-screen bg-[#1a1a1a]">
        <div class="max-w-4xl mx-auto px-8 py-12">
            <h1 class="text-4xl font-bold text-white mb-8">Créer un film</h1>
            
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
                
                @if (session('success'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500/50 rounded-lg text-green-300">
                    {{ session('success') }}
                </div>
                @endif
                
                <!-- Upload Progress Bar -->
                <div id="uploadProgress" class="hidden mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-white text-sm font-medium">Upload en cours...</span>
                        <span id="uploadPercent" class="text-white text-sm">0%</span>
                    </div>
                    <div class="w-full bg-white/10 rounded-full h-2.5">
                        <div id="uploadProgressBar" class="bg-[#0063e5] h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
                
                <form id="movieForm" method="POST" action="{{ route('admin.movies.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-white mb-2 font-semibold">Titre *</label>
                            <input type="text" name="title" value="{{ old('title') }}" required 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400">
                        </div>
                        <div>
                            <label class="block text-white mb-2 font-semibold">Année de sortie *</label>
                            <input type="date" name="release_year" value="{{ old('release_year') }}" required 
                                min="1900-01-01" max="{{ date('Y-m-d') }}"
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Description *</label>
                        <textarea name="description" rows="4" required 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400">{{ old('description') }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-white mb-2 font-semibold">Durée (minutes) *</label>
                            <input type="number" name="duration" value="{{ old('duration') }}" required 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-white mb-2 font-semibold">Affiche (image)</label>
                            <input type="file" name="poster" accept="image/*" 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#0063e5] file:text-white hover:file:bg-[#0483ee] file:cursor-pointer">
                            <p class="text-gray-400 text-sm mt-2">Formats acceptés : JPG, PNG, WebP (max 5MB)</p>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Image de fond (backdrop)</label>
                        <input type="file" name="backdrop" accept="image/*" 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#0063e5] file:text-white hover:file:bg-[#0483ee] file:cursor-pointer">
                        <p class="text-gray-400 text-sm mt-2">Formats acceptés : JPG, PNG, WebP (max 5MB)</p>
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Vidéo</label>
                        <input type="file" name="video_url" accept="video/*" 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#0063e5] file:text-white hover:file:bg-[#0483ee] file:cursor-pointer">
                        <p class="text-gray-400 text-sm mt-2">Formats acceptés : MP4, WebM, MOV (max 500MB)</p>
                    </div>
                    <div class="mb-6">
                        <label class="flex items-center text-white cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} 
                                class="mr-3 w-5 h-5 rounded bg-white/10 border-white/20 text-[#0063e5] focus:ring-[#0063e5]">
                            <span class="font-semibold">Mettre en vedette</span>
                        </label>
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Genres</label>
                        <select name="genres[]" multiple 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                            @foreach($genres as $genre)
                            <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-gray-400 text-sm mt-2">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs genres</p>
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Acteurs</label>
                        <select name="actors[]" multiple 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                            @foreach($actors as $actor)
                            <option value="{{ $actor->id }}">{{ $actor->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-gray-400 text-sm mt-2">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs acteurs</p>
                    </div>
                    <div class="mb-6">
                        <label class="block text-white mb-2 font-semibold">Réalisateurs</label>
                        <select name="directors[]" multiple 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                            @foreach($directors as $director)
                            <option value="{{ $director->id }}">{{ $director->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-gray-400 text-sm mt-2">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs réalisateurs</p>
                    </div>
                    <div class="flex gap-4">
                        <button type="submit" id="submitBtn" class="px-6 py-3 bg-[#0063e5] hover:bg-[#0483ee] text-white font-semibold rounded-lg transition">
                            Créer le film
                        </button>
                        <a href="{{ route('admin.movies.index') }}" class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-lg font-semibold transition">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('movieForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            const submitBtn = document.getElementById('submitBtn');
            const progressContainer = document.getElementById('uploadProgress');
            const progressBar = document.getElementById('uploadProgressBar');
            const progressPercent = document.getElementById('uploadPercent');
            
            // Check if there are files to upload
            const hasFiles = formData.get('poster')?.size > 0 || 
                           formData.get('backdrop')?.size > 0 || 
                           formData.get('video_url')?.size > 0;
            
            if (hasFiles) {
                // Show progress bar
                progressContainer?.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Upload en cours...';
                
                // Create XMLHttpRequest for progress tracking
                const xhr = new XMLHttpRequest();
                
                // Track upload progress
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable && progressBar && progressPercent) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        progressBar.style.width = percentComplete + '%';
                        progressPercent.textContent = Math.round(percentComplete) + '%';
                    }
                });
                
                // Handle completion
                xhr.addEventListener('load', function() {
                    if (xhr.status === 200 || xhr.status === 302) {
                        if (progressBar && progressPercent) {
                            progressBar.style.width = '100%';
                            progressPercent.textContent = '100%';
                        }
                        
                        // Reload page after short delay
                        setTimeout(() => {
                            window.location.href = '{{ route("admin.movies.index") }}';
                        }, 500);
                    } else {
                        showError('Erreur lors de l\'upload (Code: ' + xhr.status + ')');
                        resetForm();
                    }
                });
                
                // Handle errors
                xhr.addEventListener('error', function() {
                    showError('Erreur de connexion lors de l\'upload');
                    resetForm();
                });
                
                // Handle timeout
                xhr.addEventListener('timeout', function() {
                    showError('Timeout lors de l\'upload. Le fichier est peut-être trop volumineux.');
                    resetForm();
                });
                
                // Set timeout to 10 minutes for large files
                xhr.timeout = 600000; // 10 minutes
                
                // Send request
                xhr.open('POST', form.action);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.send(formData);
                
                function resetForm() {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Créer le film';
                    progressContainer?.classList.add('hidden');
                    if (progressBar && progressPercent) {
                        progressBar.style.width = '0%';
                        progressPercent.textContent = '0%';
                    }
                }
                
                function showError(message) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-lg text-red-300';
                    errorDiv.textContent = message;
                    form.insertBefore(errorDiv, form.firstChild);
                    
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 5000);
                }
            } else {
                // No files, submit normally
                form.submit();
            }
        });
    </script>
</x-app-layout>
