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
        @if($content->video_url || ($content instanceof \App\Models\Series && $content->episodes->count()))
        <div id="videoSection" class="relative w-full min-h-screen bg-black">
            <!-- Top Bar with Title and Episode Info -->
            <div class="absolute top-0 left-0 right-0 z-30 bg-gradient-to-b from-black/80 to-transparent px-6 py-4">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('catalog') }}" class="text-white hover:text-gray-300 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                    </div>
                    <button onclick="toggleEpisodeSelector()" class="text-white hover:text-gray-300 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Video Player Container -->
            <div class="relative w-full pt-16 pb-32">
                <div class="max-w-7xl mx-auto px-6">
                    <!-- Video Player -->
                    <div class="relative w-full bg-black rounded-lg overflow-hidden" style="padding-top: 56.25%;">
                        <video
                            id="videoPlayer"
                            class="absolute top-0 left-0 w-full h-full bg-black"
                            controlsList="nodownload"
                            preload="metadata"
                            @if($watchProgress && $watchProgress->progress > 0)
                            data-start-time="{{ $watchProgress->progress }}"
                            @endif
                        >
                            @if($content instanceof \App\Models\Movie && $content->video_url)
                            <source src="{{ $content->video_url_display ?? $content->video_url }}" type="video/mp4">
                            @elseif($content instanceof \App\Models\Series && $content->episodes->count())
                            @php
                                // Use current episode if available, otherwise use next episode, otherwise first episode
                                $episodeToLoad = $currentEpisode ?? $nextEpisode ?? $content->episodes->first();
                                $episodeVideoUrl = $episodeToLoad && $episodeToLoad->video_url ? ($episodeToLoad->video_url_display ?? $episodeToLoad->video_url) : '';
                            @endphp
                            @if($episodeVideoUrl)
                            <source src="{{ $episodeVideoUrl }}" type="video/mp4" data-episode-id="{{ $episodeToLoad->id }}">
                            @endif
                            @endif
                            Votre navigateur ne supporte pas la lecture de vidéos.
                        </video>
                    </div>

                    <!-- Custom Video Controls -->
                    <div id="videoControls" class="mt-4">
                        <!-- Progress Bar -->
                        <div class="relative mb-4">
                            <div class="w-full h-1 bg-gray-700 rounded-full overflow-hidden">
                                <div id="progressBar" class="h-full bg-[#0063e5] transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <input
                                type="range"
                                id="progressSlider"
                                min="0"
                                max="100"
                                value="0"
                                step="0.1"
                                class="absolute top-0 left-0 w-full h-1 opacity-0 cursor-pointer"
                                oninput="seekTo(this.value)"
                            >
                            <div class="flex justify-between items-center mt-2">
                                <span id="currentTime" class="text-gray-400 text-sm">0:00</span>
                                <span id="totalTime" class="text-gray-400 text-sm">0:00</span>
                            </div>
                        </div>

                        <!-- Playback Controls -->
                        <div class="flex items-center justify-center gap-6">
                            <button onclick="skipBackward()" class="text-white hover:text-gray-300 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                </svg>
                            </button>
                            <button onclick="rewind10()" class="text-white hover:text-gray-300 transition flex items-center gap-1">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0019 16V8a1 1 0 00-1.6-.8l-5.334 4zM4.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0011 16V8a1 1 0 00-1.6-.8l-5.334 4z" />
                                </svg>
                                <span class="text-xs">10</span>
                            </button>
                            <button onclick="togglePlayPause()" id="playPauseBtn" class="w-14 h-14 bg-white text-black rounded-full flex items-center justify-center hover:bg-gray-200 transition">
                                <svg id="playIcon" class="w-7 h-7 hidden" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                                </svg>
                                <svg id="pauseIcon" class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button onclick="forward10()" class="text-white hover:text-gray-300 transition flex items-center gap-1">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.933 12.8a1 1 0 000-1.6L6.6 7.2A1 1 0 005 8v8a1 1 0 001.6.8l5.333-4zM19.933 12.8a1 1 0 000-1.6l-5.333-4A1 1 0 0013 8v8a1 1 0 001.6.8l5.333-4z" />
                                </svg>
                                <span class="text-xs">10</span>
                            </button>
                            <button onclick="skipForward()" class="text-white hover:text-gray-300 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>

                        <!-- Right Side Controls -->
                        <div class="flex items-center justify-end gap-4 mt-4">
                            @if($content instanceof \App\Models\Series && $nextEpisode)
                            <button onclick="playNextEpisode()" id="nextEpisodeBtn" class="px-4 py-2 bg-[#0063e5] hover:bg-[#0483ee] text-white font-semibold rounded transition flex items-center gap-2">
                                <span>Épisode suivant</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            @endif
                            <button onclick="toggleMute()" id="muteBtn" class="text-white hover:text-gray-300 transition">
                                <svg id="volumeIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M6.343 6.343l8.485 8.485M6.343 17.657L4.93 19.07a1 1 0 01-1.414-1.414l1.414-1.414m14.142-8.485L19.07 4.93a1 1 0 011.414 1.414l-1.414 1.414" />
                                </svg>
                                <svg id="mutedIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                                </svg>
                            </button>
                            <button onclick="toggleFullscreen()" class="text-white hover:text-gray-300 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Episode Selector for Series -->
            @if($content instanceof \App\Models\Series && $content->episodes->count())
            <div id="episodeSelector" class="fixed top-16 right-4 z-50 bg-black/95 backdrop-blur-sm rounded-lg p-4 max-w-xs max-h-96 overflow-y-auto hidden border border-white/10">
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
            @endif
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

                    @if($content->genres->count())
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-white mb-3">Genres</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($content->genres as $genre)
                            <span class="px-3 py-1 bg-white/10 rounded-full text-white text-sm">{{ $genre->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($content->actors->count())
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-white mb-3">Distribution</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($content->actors as $actor)
                            <div class="flex flex-col items-center">
                                @if($actor->photo && $actor->photo_url)
                                <img src="{{ $actor->photo_url }}" alt="{{ $actor->name }}" class="w-20 h-20 rounded-full object-cover border-2 border-white/20 mb-2 hover:border-[#0063e5] transition">
                                @else
                                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-semibold text-xl mb-2 border-2 border-white/20">
                                    {{ strtoupper(substr($actor->name, 0, 1)) }}
                                </div>
                                @endif
                                <p class="text-gray-300 text-sm text-center">{{ $actor->name }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($content->directors->count())
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-white mb-3">Réalisateur{{ $content->directors->count() > 1 ? 's' : '' }}</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($content->directors as $director)
                            <div class="flex flex-col items-center">
                                @if($director->photo && $director->photo_url)
                                <img src="{{ $director->photo_url }}" alt="{{ $director->name }}" class="w-20 h-20 rounded-full object-cover border-2 border-white/20 mb-2 hover:border-[#0063e5] transition">
                                @else
                                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-semibold text-xl mb-2 border-2 border-white/20">
                                    {{ strtoupper(substr($director->name, 0, 1)) }}
                                </div>
                                @endif
                                <p class="text-gray-300 text-sm text-center">{{ $director->name }}</p>
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

                    @if($content->reviews->count())
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

    @if($content->video_url || ($content instanceof \App\Models\Series && $content->episodes->count()))
    <script>
        const videoPlayer = document.getElementById('videoPlayer');
        const videoControls = document.getElementById('videoControls');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const playIcon = document.getElementById('playIcon');
        const pauseIcon = document.getElementById('pauseIcon');
        const currentTimeEl = document.getElementById('currentTime');
        const totalTimeEl = document.getElementById('totalTime');
        const progressBar = document.getElementById('progressBar');
        const progressSlider = document.getElementById('progressSlider');
        const muteBtn = document.getElementById('muteBtn');
        const volumeIcon = document.getElementById('volumeIcon');
        const mutedIcon = document.getElementById('mutedIcon');
        let progressInterval;
        let currentEpisodeId = null;

        // Initialize video
        if (videoPlayer) {
            // Resume from last position if available
            const startTime = videoPlayer.dataset.startTime;
            if (startTime && startTime > 0) {
                videoPlayer.currentTime = parseInt(startTime);
            }

            // Track progress
            startProgressTracking();

            // Update time display and progress bar
            videoPlayer.addEventListener('loadedmetadata', function() {
                updateTimeDisplay();
                updateProgressBar();
            });

            videoPlayer.addEventListener('timeupdate', function() {
                updateTimeDisplay();
                updateProgressBar();
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

            // Handle volume changes
            videoPlayer.addEventListener('volumechange', function() {
                updateVolumeIcon();
            });

            // Save progress when video ends
            videoPlayer.addEventListener('ended', function() {
                saveProgress(true); // Pass true to indicate episode ended
                stopProgressTracking();

                // Auto-play next episode if available
                @if($content instanceof \App\Models\Series && $nextEpisode)
                setTimeout(() => {
                    playNextEpisode();
                }, 3000); // Wait 3 seconds before auto-playing next episode
                @endif
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

            // Update episode title in top bar
            @if($content instanceof \App\Models\Series)
            const episodeInfo = document.querySelector('#videoSection .text-gray-400');
            if (episodeInfo) {
                // Extract season and episode number from episode title or use data
                const episodeBtn = document.querySelector(`[data-episode-id="${episodeId}"]`);
                if (episodeBtn) {
                    const episodeNum = episodeBtn.querySelector('.text-gray-400')?.textContent || '';
                    episodeInfo.textContent = episodeNum + ' ' + episodeTitle;
                }
            }
            @endif

            // Update active episode button
            document.querySelectorAll('.episode-btn').forEach(btn => {
                btn.classList.remove('bg-white/20');
            });
            const activeBtn = document.querySelector(`[data-episode-id="${episodeId}"]`);
            if (activeBtn) {
                activeBtn.classList.add('bg-white/20');
            }

            // Hide episode selector
            toggleEpisodeSelector();
        }

        function updateTimeDisplay() {
            if (videoPlayer && videoPlayer.duration) {
                currentTimeEl.textContent = formatTime(videoPlayer.currentTime);
                totalTimeEl.textContent = formatTime(videoPlayer.duration);
            }
        }

        function updateProgressBar() {
            if (videoPlayer && videoPlayer.duration) {
                const progress = (videoPlayer.currentTime / videoPlayer.duration) * 100;
                progressBar.style.width = progress + '%';
                progressSlider.value = progress;
            }
        }

        function seekTo(percentage) {
            if (videoPlayer && videoPlayer.duration) {
                videoPlayer.currentTime = (percentage / 100) * videoPlayer.duration;
            }
        }

        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        }

        function skipBackward() {
            if (videoPlayer) {
                // Skip to previous episode or beginning
                videoPlayer.currentTime = 0;
            }
        }

        function rewind10() {
            if (videoPlayer) {
                videoPlayer.currentTime = Math.max(0, videoPlayer.currentTime - 10);
            }
        }

        function forward10() {
            if (videoPlayer && videoPlayer.duration) {
                videoPlayer.currentTime = Math.min(videoPlayer.duration, videoPlayer.currentTime + 10);
            }
        }

        function skipForward() {
            if (videoPlayer && videoPlayer.duration) {
                // Skip to next episode or end
                videoPlayer.currentTime = videoPlayer.duration - 1;
            }
        }

        function toggleMute() {
            if (videoPlayer) {
                videoPlayer.muted = !videoPlayer.muted;
                updateVolumeIcon();
            }
        }

        function updateVolumeIcon() {
            if (videoPlayer) {
                if (videoPlayer.muted || videoPlayer.volume === 0) {
                    volumeIcon.classList.add('hidden');
                    mutedIcon.classList.remove('hidden');
                } else {
                    volumeIcon.classList.remove('hidden');
                    mutedIcon.classList.add('hidden');
                }
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

        function saveProgress(episodeEnded = false) {
            if (!videoPlayer) return;

            const progress = Math.floor(videoPlayer.currentTime);
            const duration = videoPlayer.duration;
            const completed = episodeEnded || progress >= duration - 10; // Consider completed if within 10 seconds of end

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
            })
            .then(response => response.json())
            .then(data => {
                // If next episode info is returned, update the next episode data and button
                if (data.next_episode) {
                    nextEpisodeData = data.next_episode;
                    updateNextEpisodeButton(data.next_episode);

                    // Show/hide next episode button based on availability
                    const nextBtn = document.getElementById('nextEpisodeBtn');
                    if (nextBtn) {
                        if (data.next_episode.video_url) {
                            nextBtn.style.display = 'flex';
                        } else {
                            nextBtn.style.display = 'none';
                        }
                    }
                }
            })
            .catch(err => console.error('Error saving progress:', err));
        }

        function playNextEpisode() {
            if (nextEpisodeData && nextEpisodeData.video_url) {
                loadEpisode(nextEpisodeData.id, nextEpisodeData.video_url, nextEpisodeData.title);
            }
        }

        function updateNextEpisodeButton(nextEpisodeData) {
            if (nextEpisodeData && nextEpisodeData.video_url) {
                // Update the next episode button to use the new next episode
                const nextBtn = document.getElementById('nextEpisodeBtn');
                if (nextBtn) {
                    nextBtn.onclick = function() {
                        loadEpisode(nextEpisodeData.id, nextEpisodeData.video_url, nextEpisodeData.title);
                    };
                }
            }
        }

        // Store next episode data globally
        let nextEpisodeData = @if($content instanceof \App\Models\Series && $nextEpisode)
        {
            id: {{ $nextEpisode->id }},
            title: '{{ addslashes($nextEpisode->title) }}',
            video_url: '{{ $nextEpisode->video_url ? ($nextEpisode->video_url_display ?? $nextEpisode->video_url) : '' }}',
            season_number: {{ $nextEpisode->season_number }},
            episode_number: {{ $nextEpisode->episode_number }}
        }
        @else
        null
        @endif;

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
                rewind10();
            }
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                forward10();
            }

            // M for mute
            if (e.key === 'm' || e.key === 'M') {
                e.preventDefault();
                toggleMute();
            }

            // F for fullscreen
            if (e.key === 'f' || e.key === 'F') {
                e.preventDefault();
                toggleFullscreen();
            }
        });

        // Mark current episode as active on load
        @if($content instanceof \App\Models\Series && $content->episodes->count())
        @if($currentEpisode)
        const currentEpisodeBtn = document.querySelector(`[data-episode-id="{{ $currentEpisode->id }}"]`);
        if (currentEpisodeBtn) {
            currentEpisodeBtn.classList.add('bg-white/20');
        }
        @elseif($nextEpisode)
        const nextEpisodeBtn = document.querySelector(`[data-episode-id="{{ $nextEpisode->id }}"]`);
        if (nextEpisodeBtn) {
            nextEpisodeBtn.classList.add('bg-white/20');
        }
        @else
        const firstEpisode = document.querySelector('.episode-btn');
        if (firstEpisode) {
            firstEpisode.classList.add('bg-white/20');
        }
        @endif
        @endif
    </script>
    @endif
</x-app-layout>
