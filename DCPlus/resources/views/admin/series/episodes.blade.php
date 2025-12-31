<x-app-layout>
    <div class="min-h-screen bg-[#1a1a1a]">
        <div class="max-w-7xl mx-auto px-8 py-12">
            <h1 class="text-4xl font-bold text-white mb-8">Gérer les épisodes : {{ $series->title }}</h1>
            
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-8 mb-8">
                <h3 class="text-xl font-semibold text-white mb-6">Ajouter un nouvel épisode</h3>
                
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
                
                <form id="episodeForm" method="POST" action="{{ route('admin.series.episodes.store', $series) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-white mb-2 font-semibold">Saison *</label>
                            <input type="number" name="season_number" value="{{ old('season_number', 1) }}" required 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-white mb-2 font-semibold">Épisode *</label>
                            <input type="number" name="episode_number" value="{{ old('episode_number', 1) }}" required 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-white mb-2 font-semibold">Durée (min) *</label>
                            <input type="number" name="duration" value="{{ old('duration') }}" required 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-white mb-2 font-semibold">Date de diffusion</label>
                            <input type="date" name="air_date" value="{{ old('air_date') }}" 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-white mb-2 font-semibold">Titre *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400">
                    </div>
                    <div class="mb-4">
                        <label class="block text-white mb-2 font-semibold">Description</label>
                        <textarea name="description" rows="2" 
                            class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent placeholder-gray-400">{{ old('description') }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-white mb-2 font-semibold">Vidéo de l'épisode</label>
                            <input type="file" name="video_url" accept="video/*" 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#0063e5] file:text-white hover:file:bg-[#0483ee] file:cursor-pointer">
                            <p class="text-gray-400 text-sm mt-2">Formats acceptés : MP4, WebM, MOV (max 500MB)</p>
                        </div>
                        <div>
                            <label class="block text-white mb-2 font-semibold">Miniature (thumbnail)</label>
                            <input type="file" name="thumbnail" accept="image/*" 
                                class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#0063e5] file:text-white hover:file:bg-[#0483ee] file:cursor-pointer">
                            <p class="text-gray-400 text-sm mt-2">Formats acceptés : JPG, PNG, WebP (max 5MB)</p>
                        </div>
                    </div>
                    <button type="submit" id="submitBtn" class="px-6 py-3 bg-[#0063e5] hover:bg-[#0483ee] text-white font-semibold rounded-lg transition">
                        Ajouter l'épisode
                    </button>
                </form>
            </div>

            <script>
                document.getElementById('episodeForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const form = this;
                    const formData = new FormData(form);
                    const submitBtn = document.getElementById('submitBtn');
                    const progressContainer = document.getElementById('uploadProgress');
                    const progressBar = document.getElementById('uploadProgressBar');
                    const progressPercent = document.getElementById('uploadPercent');
                    
                    // Show progress bar
                    progressContainer.classList.remove('hidden');
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Upload en cours...';
                    
                    // Create XMLHttpRequest for progress tracking
                    const xhr = new XMLHttpRequest();
                    
                    // Track upload progress
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percentComplete = (e.loaded / e.total) * 100;
                            progressBar.style.width = percentComplete + '%';
                            progressPercent.textContent = Math.round(percentComplete) + '%';
                        }
                    });
                    
                    // Handle completion
                    xhr.addEventListener('load', function() {
                        if (xhr.status === 200) {
                            progressBar.style.width = '100%';
                            progressPercent.textContent = '100%';
                            
                            // Check if response indicates success
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    // Reload page after short delay
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 500);
                                } else {
                                    // Handle validation errors
                                    if (response.errors) {
                                        let errorMsg = 'Erreurs de validation:\n';
                                        for (const field in response.errors) {
                                            errorMsg += response.errors[field].join('\n') + '\n';
                                        }
                                        showError(errorMsg);
                                    } else {
                                        showError(response.message || 'Erreur lors de l\'upload');
                                    }
                                    resetForm();
                                }
                            } catch (e) {
                                // If response is HTML (redirect or success message), reload page
                                if (xhr.responseText.includes('success') || xhr.getResponseHeader('Location')) {
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 500);
                                } else if (xhr.responseText.includes('error') || xhr.responseText.includes('validation')) {
                                    // Try to extract error message from HTML
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(xhr.responseText, 'text/html');
                                    const errorElements = doc.querySelectorAll('.text-red-300, .text-red-400, [class*="error"]');
                                    if (errorElements.length > 0) {
                                        showError(Array.from(errorElements).map(el => el.textContent).join('\n'));
                                    } else {
                                        showError('Erreur lors de l\'upload');
                                    }
                                    resetForm();
                                } else {
                                    // Assume success if we get here
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 500);
                                }
                            }
                        } else if (xhr.status === 422) {
                            // Validation errors
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.errors) {
                                    let errorMsg = 'Erreurs de validation:\n';
                                    for (const field in response.errors) {
                                        errorMsg += response.errors[field].join('\n') + '\n';
                                    }
                                    showError(errorMsg);
                                }
                            } catch (e) {
                                showError('Erreur de validation');
                            }
                            resetForm();
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
                        submitBtn.textContent = 'Ajouter l\'épisode';
                        progressContainer.classList.add('hidden');
                        progressBar.style.width = '0%';
                        progressPercent.textContent = '0%';
                    }
                    
                    function showError(message) {
                        // Create error message
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'mt-4 p-4 bg-red-500/20 border border-red-500/50 rounded-lg text-red-300';
                        errorDiv.textContent = message;
                        form.insertBefore(errorDiv, form.firstChild);
                        
                        // Remove error after 5 seconds
                        setTimeout(() => {
                            errorDiv.remove();
                        }, 5000);
                    }
                });
            </script>

            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-8">
                <h3 class="text-xl font-semibold text-white mb-6">Épisodes</h3>
                <div class="space-y-6">
                    @foreach($episodes->groupBy('season_number') as $season => $seasonEpisodes)
                    <div>
                        <h4 class="text-lg font-semibold text-white mb-4">Saison {{ $season }}</h4>
                        <div class="space-y-2">
                            @foreach($seasonEpisodes as $episode)
                            <div class="bg-white/5 p-4 rounded-lg hover:bg-white/10 transition">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-4">
                                        <span class="text-gray-400 text-sm font-medium">S{{ $episode->season_number }}E{{ $episode->episode_number }}</span>
                                        <span class="text-white font-medium">{{ $episode->title }}</span>
                                        <span class="text-gray-400 text-sm">({{ $episode->duration }} min)</span>
                                    </div>
                                </div>
                                @if($episode->description)
                                <p class="text-gray-400 text-sm mt-2">{{ Str::limit($episode->description, 150) }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
