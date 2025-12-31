<x-app-layout>
    <style>
        /* Hide navigation on content page with video */
        nav {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 40;
            background: transparent !important;
            border: none !important;
        }
        nav a, nav button, nav span {
            text-shadow: 0 2px 4px rgba(0,0,0,0.8);
        }
    </style>
    <div class="min-h-screen bg-[#0a0a0a] text-white">
        <!-- Full-Screen Video Player Section -->
        @if($content->video_url || ($content instanceof \App\Models\Series && $content->episodes->count() > 0))
        <div id="videoSection" class="relative w-full h-screen overflow-hidden">
            <!-- Backdrop Image -->
            @if($content->backdrop)
            <div class="absolute inset-0">
                <img src="{{ $content->backdrop_url ?? $content->backdrop }}" alt="{{ $content->title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/60 to-black/90"></div>
            </div>
            @else
            <div class="absolute inset-0 bg-gradient-to-br from-[#0063e5] via-[#764ba2] to-black"></div>
            @endif

            <!-- Video Player Container -->
            <div class="relative z-20 h-full flex items-center justify-center px-4">
                <div class="w-full max-w-7xl">
                    <!-- Video Player -->
                    <div class="relative w-full" style="padding-top: 56.25%;">
                        <video 
                            id="videoPlayer" 
                            class="absolute top-0 left-0 w-full h-full bg-black rounded-lg shadow-2xl"
                            controls
                            controlsList="nodownload"
                            preload="metadata"
                            @if($watchProgress && $watchProgress->progress > 0)
                            data-start-time="{{ $watchProgress->progress }}"
                            @endif
                        >
                            @if($content instanceof \App\Models\Movie && $content->video_url)
                            <source src="{{ $content->video_url_display ?? $content->video_url }}" type="video/mp4">
                            @elseif($content instanceof \App\Models\Series && $content->episodes->count() > 0)
                            @php
                                $firstEpisode = $content->episodes->first();
                                $episodeVideoUrl = $firstEpisode && $firstEpisode->video_url ? ($firstEpisode->video_url_display ?? $firstEpisode->video_url) : '';
                            @endphp
                            @if($episodeVideoUrl)
                            <source src="{{ $episodeVideoUrl }}" type="video/mp4" data-episode-id="{{ $firstEpisode->id }}">
                            @endif
                            @endif
                            Votre navigateur ne supporte pas la lecture de vidéos.
                        </video>
                    </div>

                    <!-- Video Controls Overlay -->
                    <div id="videoControls" class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/90 to-transparent transition-opacity duration-300">
                        <div class="max-w-7xl mx-auto flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <button onclick="togglePlayPause()" id="playPauseBtn" class="w-12 h-12 bg-white text-black rounded-full flex items-center justify-center hover:bg-gray-200 transition">
                                    <svg id="playIcon" class="w-6 h-6 hidden" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                                    </svg>
                                    <svg id="pauseIcon" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div class="text-white text-sm">
                                    <span id="currentTime">0:00</span> / <span id="totalTime">0:00</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <button onclick="toggleFullscreen()" class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Episode Selector for Series (Top Right) -->
            @if($content instanceof \App\Models\Series && $content->episodes->count() > 0)
            <div id="episodeSelector" class="absolute top-20 right-4 z-30 bg-black/90 backdrop-blur-sm rounded-lg p-4 max-w-xs max-h-96 overflow-y-auto hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-white font-semibold text-lg">Épisodes</h3>
                    <button onclick="toggleEpisodeSelector()" class="text-white hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-2">
                    @foreach($content->episodes->groupBy('season_number') as $season => $episodes)
                    <div class="mb-4">
                        <h4 class="text-gray-300 text-sm font-semibold mb-2 px-2">Saison {{ $season }}</h4>
                        @foreach($episodes as $episode)
                        <button 
                            onclick="loadEpisode({{ $episode->id }}, '{{ $episode->video_url ? ($episode->video_url_display ?? $episode->video_url) : '' }}', '{{ $episode->title }}')"
                            class="w-full text-left px-3 py-2 rounded hover:bg-white/10 transition text-white text-sm flex items-center gap-2 episode-btn"
                            data-episode-id="{{ $episode->id }}"
                        >
                            <span class="text-gray-400 text-xs w-8">E{{ $episode->episode_number }}</span>
                            <span class="flex-1">{{ $episode->title }}</span>
                            @if($episode->video_url)
                            <svg class="w-4 h-4 text-[#0063e5]" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                            </svg>
                            @endif
                        </button>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
            <button onclick="toggleEpisodeSelector()" class="absolute top-20 right-4 z-30 bg-black/80 backdrop-blur-sm text-white px-4 py-2 rounded-lg hover:bg-black/90 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                Épisodes
            </button>
            @endif

            <!-- Back Button -->
            <a href="{{ route('catalog') }}" class="absolute top-4 left-4 z-30 bg-black/60 backdrop-blur-sm text-white px-4 py-2 rounded-lg hover:bg-black/80 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Retour
            </a>
        </div>
        @else
        <!-- No Video Available - Show Hero Banner Instead -->
        <div class="relative h-[60vh] overflow-hidden">
            @if($content->backdrop)
            <div class="absolute inset-0">
                <img src="{{ $content->backdrop_url ?? $content->backdrop }}" alt="{{ $content->title }}" class="w-full h-full object-cover">
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
                        <button disabled class="px-6 py-3 bg-gray-500 text-white font-semibold rounded cursor-not-allowed flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                            </svg>
                            Vidéo non disponible
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Content Details Section -->
        <div class="max-w-7xl mx-auto px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-1">
                    @if($content->poster)
                    <img src="{{ $content->poster_url ?? $content->poster }}" alt="{{ $content->title }}" class="w-full rounded-lg shadow-lg">
                    @endif
                </div>
                
                <div class="md:col-span-3">
                    <div class="flex items-center gap-4 mb-6">
                        <h2 class="text-3xl font-bold text-white">{{ $content->title }}</h2>
                        @auth
                        @if(auth()->user()->profiles()->exists())
                        @if($isInWatchlist)
                        <form action="{{ route('watchlist.remove', ['type' => $content instanceof \App\Models\Movie ? 'movie' : 'series', 'id' => $content->id]) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600/80 backdrop-blur-sm border border-red-500/50 text-white font-semibold rounded hover:bg-red-600 transition">
                                ✗ Retirer de ma liste
                            </button>
                        </form>
                        @else
                        <form action="{{ route('watchlist.add', ['type' => $content instanceof \App\Models\Movie ? 'movie' : 'series', 'id' => $content->id]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-white/20 backdrop-blur-sm border border-white/30 text-white font-semibold rounded hover:bg-white/30 transition">
                                + Ma liste
                            </button>
                        </form>
                        @endif
                        @endif
                        @endauth
                    </div>

                    <p class="text-lg text-gray-300 mb-6 leading-relaxed">{{ $content->description }}</p>

                    <div class="flex flex-wrap gap-4 mb-6 text-gray-300">
                        <span>{{ $content->release_year }}</span>
                        @if($content instanceof \App\Models\Movie)
                        <span>{{ $content->duration }} min</span>
                        @else
                        <span>{{ $content->seasons }} saisons</span>
                        @endif
                        <span class="text-yellow-400">★ {{ $content->rating }}</span>
                    </div>

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

                    @if($content->directors->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-white mb-3">Réalisateur{{ $content->directors->count() > 1 ? 's' : '' }}</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($content->directors as $director)
                            <span class="text-gray-300">{{ $director->name }}</span>@if(!$loop->last),@endif
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

    @if($content->video_url || ($content instanceof \App\Models\Series && $content->episodes->count() > 0))
    <script>
        const videoPlayer = document.getElementById('videoPlayer');
        const videoControls = document.getElementById('videoControls');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const playIcon = document.getElementById('playIcon');
        const pauseIcon = document.getElementById('pauseIcon');
        const currentTimeEl = document.getElementById('currentTime');
        const totalTimeEl = document.getElementById('totalTime');
        let progressInterval;
        let currentEpisodeId = null;
        let controlsTimeout;

        // Initialize video
        if (videoPlayer) {
            // Resume from last position if available
            const startTime = videoPlayer.dataset.startTime;
            if (startTime && startTime > 0) {
                videoPlayer.currentTime = parseInt(startTime);
            }

            // Auto-play video
            videoPlayer.play().catch(err => {
                console.error('Error playing video:', err);
            });

            // Track progress
            startProgressTracking();

            // Update time display
            videoPlayer.addEventListener('loadedmetadata', function() {
                updateTimeDisplay();
            });

            videoPlayer.addEventListener('timeupdate', function() {
                updateTimeDisplay();
            });

            // Handle play/pause icon
            videoPlayer.addEventListener('play', function() {
                playIcon.classList.add('hidden');
                pauseIcon.classList.remove('hidden');
            });

            videoPlayer.addEventListener('pause', function() {
                playIcon.classList.remove('hidden');
                pauseIcon.classList.add('hidden');
            });

            // Show/hide controls on mouse movement
            videoPlayer.addEventListener('mousemove', function() {
                showControls();
            });

            videoPlayer.addEventListener('mouseleave', function() {
                hideControls();
            });

            // Auto-hide controls after 3 seconds
            videoPlayer.addEventListener('play', function() {
                setTimeout(hideControls, 3000);
            });

            // Save progress when video ends
            videoPlayer.addEventListener('ended', function() {
                saveProgress();
                stopProgressTracking();
            });

            // Save progress when leaving page
            window.addEventListener('beforeunload', function() {
                saveProgress();
            });
        }

        function togglePlayPause() {
            if (videoPlayer.paused) {
                videoPlayer.play();
            } else {
                videoPlayer.pause();
            }
            showControls();
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                videoPlayer.requestFullscreen().catch(err => {
                    console.error('Error entering fullscreen:', err);
                });
            } else {
                document.exitFullscreen();
            }
        }

        function toggleEpisodeSelector() {
            const selector = document.getElementById('episodeSelector');
            if (selector) {
                selector.classList.toggle('hidden');
            }
        }

        function loadEpisode(episodeId, videoUrl, episodeTitle) {
            if (!videoUrl) {
                alert('Vidéo non disponible pour cet épisode');
                return;
            }
            
            currentEpisodeId = episodeId;
            const source = videoPlayer.querySelector('source');
            if (source) {
                source.src = videoUrl;
                source.setAttribute('data-episode-id', episodeId);
                videoPlayer.load();
                videoPlayer.play().catch(err => console.error('Error playing episode:', err));
            }

            // Update active episode button
            document.querySelectorAll('.episode-btn').forEach(btn => {
                btn.classList.remove('bg-white/20');
            });
            document.querySelector(`[data-episode-id="${episodeId}"]`).classList.add('bg-white/20');

            // Hide episode selector
            toggleEpisodeSelector();
        }

        function updateTimeDisplay() {
            if (videoPlayer && videoPlayer.duration) {
                currentTimeEl.textContent = formatTime(videoPlayer.currentTime);
                totalTimeEl.textContent = formatTime(videoPlayer.duration);
            }
        }

        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        }

        function showControls() {
            if (videoControls) {
                videoControls.classList.remove('opacity-0');
                videoControls.classList.add('opacity-100');
            }
            clearTimeout(controlsTimeout);
            controlsTimeout = setTimeout(hideControls, 3000);
        }

        function hideControls() {
            if (videoControls && !videoPlayer.paused) {
                videoControls.classList.remove('opacity-100');
                videoControls.classList.add('opacity-0');
            }
        }

        function startProgressTracking() {
            progressInterval = setInterval(() => {
                saveProgress();
            }, 5000); // Save every 5 seconds
        }

        function stopProgressTracking() {
            if (progressInterval) {
                clearInterval(progressInterval);
            }
        }

        function saveProgress() {
            if (!videoPlayer) return;
            
            const progress = Math.floor(videoPlayer.currentTime);
            const duration = videoPlayer.duration;
            const completed = progress >= duration - 10; // Consider completed if within 10 seconds of end
            
            // For series, we might need to track episode progress separately
            const episodeId = videoPlayer.querySelector('source')?.getAttribute('data-episode-id');
            
            fetch('{{ route("content.watch", $content->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    progress: progress,
                    completed: completed,
                    episode_id: episodeId || null
                })
            }).catch(err => console.error('Error saving progress:', err));
        }

        // Keyboard controls
        document.addEventListener('keydown', function(e) {
            if (!videoPlayer) return;

            // Space bar to play/pause
            if (e.key === ' ' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                togglePlayPause();
            }

            // Arrow keys for seeking
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                videoPlayer.currentTime = Math.max(0, videoPlayer.currentTime - 10);
                showControls();
            }
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                videoPlayer.currentTime = Math.min(videoPlayer.duration, videoPlayer.currentTime + 10);
                showControls();
            }

            // F for fullscreen
            if (e.key === 'f' || e.key === 'F') {
                e.preventDefault();
                toggleFullscreen();
            }
        });

        // Mark first episode as active on load
        @if($content instanceof \App\Models\Series && $content->episodes->count() > 0)
        const firstEpisode = document.querySelector('.episode-btn');
        if (firstEpisode) {
            firstEpisode.classList.add('bg-white/20');
        }
        @endif
    </script>
    @endif
</x-app-layout>
