<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DCPlus - Streaming Platform</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
            @vite(['resources/css/app.css', 'resources/js/app.js'])
            <style>
        .poster-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 4px;
            padding: 20px;
            opacity: 0.4;
            position: absolute;
            inset: 0;
            overflow: hidden;
        }
        .poster-item {
            aspect-ratio: 2/3;
            border-radius: 4px;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
        }
        .poster-item:nth-child(2n) {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .poster-item:nth-child(3n) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .poster-item:nth-child(4n) {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        .poster-item:nth-child(5n) {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        .poster-item:nth-child(6n) {
            background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
        }
        .poster-item:nth-child(7n) {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }
        .hero-overlay {
            background: rgba(26, 26, 26, 0.85);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
            </style>
    </head>
<body class="bg-[#1a1a1a] text-white font-sans overflow-x-hidden">
    <!-- Navigation -->
    <nav class="absolute top-0 left-0 right-0 z-50 px-8 py-6">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="text-3xl font-bold text-white tracking-tight">DCPlus</div>
            <div class="flex items-center gap-4">
                    @auth
                    <a href="{{ route('catalog') }}" class="px-6 py-2 bg-[#0063e5] hover:bg-[#0483ee] text-white font-medium rounded transition">
                        Accéder au catalogue
                        </a>
                    @else
                    <a href="{{ route('login') }}" class="px-6 py-2 bg-[#1a1a1a] border border-white/20 hover:border-white/40 text-white font-medium rounded transition">
                        S'IDENTIFIER
                    </a>
                    <a href="{{ route('register') }}" class="px-6 py-2 bg-[#0063e5] hover:bg-[#0483ee] text-white font-medium rounded transition">
                        S'INSCRIRE
                    </a>
                    @endauth
            </div>
        </div>
                </nav>



    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center z-10 px-4">
        <div class="max-w-2xl mx-auto w-full">
            <!-- Overlay Card -->
            <div class="hero-overlay rounded-2xl p-8 md:p-12 shadow-2xl">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight text-center">
                    Grands films, nouvelles productions<br>originales et séries exclusives
                </h1>
                <p class="text-lg md:text-xl mb-8 text-gray-300 text-center">
                    À partir de <span class="text-white font-bold">6,99 €</span> par mois. Sans frais supplémentaires. Sans engagement*.
                </p>
                
                @guest
                <div class="max-w-md mx-auto">
                    <form action="{{ route('register') }}" method="GET" class="flex flex-col sm:flex-row gap-3 mb-4">
                        <input 
                            type="email" 
                            placeholder="Adresse e-mail" 
                            class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0063e5] focus:border-transparent"
                            required
                        >
                        <button type="submit" class="px-8 py-3 bg-[#0063e5] hover:bg-[#0483ee] text-white font-semibold rounded-lg transition whitespace-nowrap">
                            S'INSCRIRE
                        </button>
                    </form>
                    <p class="text-xs text-gray-400 text-center">
                        * La résiliation prend effet à la fin de la période d'abonnement en cours. Abonnement requis. 6,99 €/mois pour Standard avec pub.
                    </p>
                </div>
                @endguest
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="relative py-20 px-8 z-10">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold mb-12 text-center text-white">Regardez selon vos envies</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-8">
                    <div class="w-16 h-16 mx-auto mb-4 bg-[#0063e5] rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Un divertissement sans fin</h3>
                    <p class="text-gray-400">Découvrez des milliers d'heures de séries, films et productions originales.</p>
                </div>
                <div class="text-center bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-8">
                    <div class="w-16 h-16 mx-auto mb-4 bg-[#0063e5] rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Disponible sur vos appareils préférés</h3>
                    <p class="text-gray-400">Utilisez jusqu'à 4 appareils compatibles en simultané.</p>
                </div>
                <div class="text-center bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-8">
                    <div class="w-16 h-16 mx-auto mb-4 bg-[#0063e5] rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Un contrôle parental intuitif</h3>
                    <p class="text-gray-400">Protégez votre famille avec un contrôle parental simple d'utilisation.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="relative py-20 px-8 z-10">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold mb-12 text-center text-white">Choisissez votre abonnement</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Plan Standard avec pub -->
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-8 hover:border-[#0063e5] transition">
                    <h3 class="text-2xl font-bold mb-4 text-white">Standard avec pub</h3>
                    <div class="text-4xl font-bold mb-2 text-white">6,99 €<span class="text-lg text-gray-400">/mois</span></div>
                    <ul class="space-y-3 mt-6 text-gray-300">
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Résolution jusqu'à 1080p Full HD
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                            2 lectures simultanées
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                            Son surround 5.1
                        </li>
                    </ul>
                    @guest
                    <a href="{{ route('register') }}" class="mt-6 block w-full text-center px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg transition">
                        Commencer
                    </a>
                    @endguest
                </div>

                <!-- Plan Standard -->
                <div class="bg-white/5 backdrop-blur-sm border-2 border-[#0063e5] rounded-lg p-8 relative">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-[#0063e5] text-white px-4 py-1 rounded-full text-sm font-semibold">
                        Populaire
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-white">Standard</h3>
                    <div class="text-4xl font-bold mb-2 text-white">10,99 €<span class="text-lg text-gray-400">/mois</span></div>
                    <ul class="space-y-3 mt-6 text-gray-300">
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Résolution jusqu'à 1080p Full HD
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            2 lectures simultanées
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Téléchargements hors ligne
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Sans publicité
                        </li>
                    </ul>
                    @guest
                    <a href="{{ route('register') }}" class="mt-6 block w-full text-center px-6 py-3 bg-[#0063e5] hover:bg-[#0483ee] rounded-lg transition">
                        Commencer
                    </a>
                    @endguest
                </div>

                <!-- Plan Premium -->
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-8 hover:border-[#0063e5] transition">
                    <h3 class="text-2xl font-bold mb-4 text-white">Premium</h3>
                    <div class="text-4xl font-bold mb-2 text-white">15,99 €<span class="text-lg text-gray-400">/mois</span></div>
                    <ul class="space-y-3 mt-6 text-gray-300">
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Résolution jusqu'à 4K Ultra HD
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                            4 lectures simultanées
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                            Son Dolby Atmos
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                            Sans publicité
                        </li>
                    </ul>
                    @guest
                    <a href="{{ route('register') }}" class="mt-6 block w-full text-center px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg transition">
                        Commencer
                    </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative border-t border-white/10 py-12 px-8 z-10">
        <div class="max-w-6xl mx-auto">
            <div class="text-2xl font-bold mb-6 text-white">DCPlus</div>
            <div class="grid md:grid-cols-4 gap-8 text-sm text-gray-400">
                <div>
                    <h4 class="text-white font-semibold mb-3">En savoir plus</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition">Conditions générales</a></li>
                        <li><a href="#" class="hover:text-white transition">Politique de confidentialité</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3">Aide</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition">Centre d'aide</a></li>
                        <li><a href="#" class="hover:text-white transition">Appareils compatibles</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3">À propos</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition">À propos de DCPlus</a></li>
                        <li><a href="#" class="hover:text-white transition">Carrières</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-white/10 text-sm text-gray-500 text-center">
                © 2025 DCPlus. Tous droits réservés.
            </div>
        </div>
    </footer>
    </body>
</html>
