<x-app-layout>
    <div class="min-h-screen bg-[#1a1a1a]">
        <!-- Hero Banner -->
        <div class="relative h-[50vh] md:h-[60vh] overflow-hidden">
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
                        <div class="flex gap-4">
                            @if($content->video_url || ($content instanceof \App\Models\Series && $content->episodes->count() > 0))
                            <button onclick="openPlayer()" class="px-6 py-3 bg-white text-black font-semibold rounded hover:bg-gray-200 transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                                </svg>
                                Regarder
                            </button>
                            @else
                            <button disabled class="px-6 py-3 bg-gray-500 text-white font-semibold rounded cursor-not-allowed flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                </svg>
                                Vidéo non disponible
                            </button>
                            @endif
                            @auth
                            @if(auth()->user()->profiles()->exists())
                            @if($isInWatchlist)
                            <form action="{{ route('watchlist.remove', ['type' => $content instanceof \App\Models\Movie ? 'movie' : 'series', 'id' => $content->id]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-6 py-3 bg-red-600/80 backdrop-blur-sm border border-red-500/50 text-white font-semibold rounded hover:bg-red-600 transition">
                                    ✗ Retirer de ma liste
                                </button>
                            </form>
                            @else
                            <form action="{{ route('watchlist.add', ['type' => $content instanceof \App\Models\Movie ? 'movie' : 'series', 'id' => $content->id]) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-6 py-3 bg-white/20 backdrop-blur-sm border border-white/30 text-white font-semibold rounded hover:bg-white/30 transition">
                                    + Ma liste
                                </button>
                            </form>
                            @endif
                            @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Details -->
        <div class="max-w-7xl mx-auto px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-1">
                    @if($content->poster)
                    <img src="{{ $content->poster_url ?? $content->poster }}" alt="{{ $content->title }}" class="w-full rounded-lg shadow-lg">
                    @endif
                </div>
                
                <div class="md:col-span-3">
                    <p class="text-lg text-gray-300 mb-6 leading-relaxed">{{ $content->description }}</p>

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

                    @if($content instanceof \App\Models\Series && $content->episodes->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Épisodes</h3>
                        <div class="space-y-3">
                            @foreach($content->episodes->groupBy('season_number') as $season => $episodes)
                            <div class="mb-6">
                                <h4 class="text-md font-semibold text-white mb-3">Saison {{ $season }}</h4>
                                <div class="space-y-2">
                                    @foreach($episodes as $episode)
                                    <div 
                                        onclick="playEpisode({{ $episode->id }}, '{{ $episode->video_url ? ($episode->video_url_display ?? $episode->video_url) : '' }}')"
                                        class="bg-white/5 p-4 rounded hover:bg-white/10 transition cursor-pointer"
                                    >
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center gap-4">
                                                <span class="text-gray-400 text-sm">E{{ $episode->episode_number }}</span>
                                                <span class="text-white font-medium">{{ $episode->title }}</span>
                                                @if($episode->video_url)
                                                <svg class="w-5 h-5 text-[#0063e5]" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                                                </svg>
                                                @endif
                                            </div>
                                            <span class="text-gray-400 text-sm">{{ $episode->duration }} min</span>
                                        </div>
                                        @if($episode->description)
                                        <p class="text-gray-400 text-sm mt-2">{{ Str::limit($episode->description, 100) }}</p>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
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

    <!-- Video Player Modal -->
    @if($content->video_url || ($content instanceof \App\Models\Series && $content->episodes->count() > 0))
    <div id="videoPlayerModal" class="fixed inset-0 z-50 hidden bg-black">
        <div class="relative w-full h-full flex items-center justify-center">
            <!-- Close Button -->
            <button onclick="closePlayer()" class="absolute top-4 right-4 z-50 text-white hover:text-gray-300 transition p-2 bg-black/50 rounded-full backdrop-blur-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Episode Selector for Series -->
            @if($content instanceof \App\Models\Series && $content->episodes->count() > 0)
            <div id="episodeSelector" class="absolute top-4 left-4 z-50 bg-black/80 backdrop-blur-sm rounded-lg p-4 max-w-xs max-h-96 overflow-y-auto">
                <h3 class="text-white font-semibold mb-3">Sélectionner un épisode</h3>
                <div class="space-y-2">
                    @foreach($content->episodes->groupBy('season_number') as $season => $episodes)
                    <div class="mb-4">
                        <h4 class="text-gray-300 text-sm font-semibold mb-2">Saison {{ $season }}</h4>
                        @foreach($episodes as $episode)
                        <button 
                            onclick="loadEpisode({{ $episode->id }}, '{{ $episode->video_url ? ($episode->video_url_display ?? $episode->video_url) : '' }}')"
                            class="w-full text-left px-3 py-2 rounded hover:bg-white/10 transition text-white text-sm"
                        >
                            <div class="flex items-center gap-2">
                                <span class="text-gray-400">E{{ $episode->episode_number }}</span>
                                <span>{{ $episode->title }}</span>
                            </div>
                        </button>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Video Player Container -->
            <div class="w-full h-full max-w-7xl mx-auto px-4">
                <div class="relative w-full" style="padding-top: 56.25%;">
                    <video 
                        id="videoPlayer" 
                        class="absolute top-0 left-0 w-full h-full bg-black"
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
            </div>
        </div>
    </div>

    <script>
        const videoPlayer = document.getElementById('videoPlayer');
        const modal = document.getElementById('videoPlayerModal');
        let progressInterval;
        let currentEpisodeId = null;

        function openPlayer() {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Start video
            if (videoPlayer) {
                // Resume from last position if available
                const startTime = videoPlayer.dataset.startTime;
                if (startTime && startTime > 0) {
                    videoPlayer.currentTime = parseInt(startTime);
                }
                
                videoPlayer.play().catch(err => {
                    console.error('Error playing video:', err);
                });
                
                // Track progress
                startProgressTracking();
            }
        }

        function closePlayer() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            
            if (videoPlayer) {
                videoPlayer.pause();
                saveProgress();
                stopProgressTracking();
            }
        }

        function playEpisode(episodeId, videoUrl) {
            if (!videoUrl) {
                alert('Vidéo non disponible pour cet épisode');
                return;
            }
            
            // Open player if not already open
            if (modal.classList.contains('hidden')) {
                openPlayer();
            }
            
            loadEpisode(episodeId, videoUrl);
        }

        function loadEpisode(episodeId, videoUrl) {
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

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closePlayer();
            }
            
            // Space bar to play/pause
            if (e.key === ' ' && !modal.classList.contains('hidden')) {
                e.preventDefault();
                if (videoPlayer) {
                    if (videoPlayer.paused) {
                        videoPlayer.play();
                    } else {
                        videoPlayer.pause();
                    }
                }
            }
        });

        // Save progress when video ends
        if (videoPlayer) {
            videoPlayer.addEventListener('ended', function() {
                saveProgress();
                stopProgressTracking();
            });

            // Save progress when leaving page
            window.addEventListener('beforeunload', function() {
                saveProgress();
            });

            // Hide episode selector when clicking on video
            videoPlayer.addEventListener('click', function() {
                const selector = document.getElementById('episodeSelector');
                if (selector) {
                    selector.style.display = selector.style.display === 'none' ? 'block' : 'none';
                }
            });
        }
    </script>
    @endif
</x-app-layout>
