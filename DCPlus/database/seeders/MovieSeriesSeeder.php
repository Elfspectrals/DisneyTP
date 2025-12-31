<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Series;
use App\Models\Genre;
use App\Models\Actor;
use App\Models\Director;
use App\Models\Episode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MovieSeriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Genres
        $genres = [
            ['name' => 'Action', 'slug' => 'action', 'description' => 'High-energy films with thrilling sequences'],
            ['name' => 'Comedy', 'slug' => 'comedy', 'description' => 'Light-hearted and humorous entertainment'],
            ['name' => 'Drama', 'slug' => 'drama', 'description' => 'Serious, plot-driven presentations'],
            ['name' => 'Sci-Fi', 'slug' => 'sci-fi', 'description' => 'Science fiction and futuristic themes'],
            ['name' => 'Horror', 'slug' => 'horror', 'description' => 'Scary and suspenseful content'],
            ['name' => 'Thriller', 'slug' => 'thriller', 'description' => 'Suspenseful and exciting stories'],
            ['name' => 'Romance', 'slug' => 'romance', 'description' => 'Love stories and romantic themes'],
            ['name' => 'Adventure', 'slug' => 'adventure', 'description' => 'Exciting journeys and quests'],
            ['name' => 'Fantasy', 'slug' => 'fantasy', 'description' => 'Magical and supernatural elements'],
            ['name' => 'Crime', 'slug' => 'crime', 'description' => 'Criminal activities and investigations'],
        ];

        $genreModels = [];
        foreach ($genres as $genre) {
            $genreModels[$genre['name']] = Genre::firstOrCreate(
                ['slug' => $genre['slug']],
                $genre
            );
        }

        // Create Actors
        $actors = [
            ['name' => 'Tom Hanks', 'slug' => 'tom-hanks', 'bio' => 'Academy Award-winning actor', 'birth_date' => '1956-07-09'],
            ['name' => 'Leonardo DiCaprio', 'slug' => 'leonardo-dicaprio', 'bio' => 'Oscar-winning actor and environmental activist', 'birth_date' => '1974-11-11'],
            ['name' => 'Scarlett Johansson', 'slug' => 'scarlett-johansson', 'bio' => 'Acclaimed actress and producer', 'birth_date' => '1984-11-22'],
            ['name' => 'Chris Evans', 'slug' => 'chris-evans', 'bio' => 'Actor known for superhero roles', 'birth_date' => '1981-06-13'],
            ['name' => 'Emma Stone', 'slug' => 'emma-stone', 'bio' => 'Oscar-winning actress', 'birth_date' => '1988-11-06'],
            ['name' => 'Ryan Gosling', 'slug' => 'ryan-gosling', 'bio' => 'Academy Award-nominated actor', 'birth_date' => '1980-11-12'],
            ['name' => 'Jennifer Lawrence', 'slug' => 'jennifer-lawrence', 'bio' => 'Oscar-winning actress', 'birth_date' => '1990-08-15'],
            ['name' => 'Brad Pitt', 'slug' => 'brad-pitt', 'bio' => 'Academy Award-winning actor and producer', 'birth_date' => '1963-12-18'],
            ['name' => 'Meryl Streep', 'slug' => 'meryl-streep', 'bio' => 'Most nominated actor in Academy Award history', 'birth_date' => '1949-06-22'],
            ['name' => 'Denzel Washington', 'slug' => 'denzel-washington', 'bio' => 'Two-time Academy Award winner', 'birth_date' => '1954-12-28'],
        ];

        $actorModels = [];
        foreach ($actors as $actor) {
            $actorModels[$actor['name']] = Actor::firstOrCreate(
                ['slug' => $actor['slug']],
                $actor
            );
        }

        // Create Directors
        $directors = [
            ['name' => 'Christopher Nolan', 'slug' => 'christopher-nolan', 'bio' => 'Acclaimed director known for complex narratives', 'birth_date' => '1970-07-30'],
            ['name' => 'Steven Spielberg', 'slug' => 'steven-spielberg', 'bio' => 'Legendary filmmaker and producer', 'birth_date' => '1946-12-18'],
            ['name' => 'Quentin Tarantino', 'slug' => 'quentin-tarantino', 'bio' => 'Award-winning director and screenwriter', 'birth_date' => '1963-03-27'],
            ['name' => 'Martin Scorsese', 'slug' => 'martin-scorsese', 'bio' => 'Academy Award-winning director', 'birth_date' => '1942-11-17'],
            ['name' => 'David Fincher', 'slug' => 'david-fincher', 'bio' => 'Director known for dark thrillers', 'birth_date' => '1962-08-28'],
            ['name' => 'Ridley Scott', 'slug' => 'ridley-scott', 'bio' => 'Acclaimed director of epic films', 'birth_date' => '1937-11-30'],
            ['name' => 'James Cameron', 'slug' => 'james-cameron', 'bio' => 'Director of blockbuster films', 'birth_date' => '1954-08-16'],
            ['name' => 'Denis Villeneuve', 'slug' => 'denis-villeneuve', 'bio' => 'Acclaimed Canadian director', 'birth_date' => '1967-10-03'],
        ];

        $directorModels = [];
        foreach ($directors as $director) {
            $directorModels[$director['name']] = Director::firstOrCreate(
                ['slug' => $director['slug']],
                $director
            );
        }

        // Sample video URL (Big Buck Bunny - public domain)
        $sampleVideoUrl = 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4';
        
        // Create Movies
        $movies = [
            [
                'title' => 'Inception',
                'description' => 'A skilled thief is given a chance at redemption if he can complete an impossible task: inception, the implantation of another person\'s idea into a target\'s subconscious.',
                'duration' => 148,
                'release_year' => 2010,
                'poster' => 'https://image.tmdb.org/t/p/w500/oYuLEt3zVCKq57qu2F8dT7NIa6f.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/s3TBrRGB1iav7gFOCNx3H31Moib.jpg',
                'video_url' => $sampleVideoUrl,
                'rating' => 8.8,
                'views' => 1500000,
                'is_featured' => true,
                'genres' => ['Action', 'Sci-Fi', 'Thriller'],
                'actors' => ['Leonardo DiCaprio', 'Tom Hanks', 'Scarlett Johansson'],
                'directors' => ['Christopher Nolan'],
            ],
            [
                'title' => 'The Dark Knight',
                'description' => 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.',
                'duration' => 152,
                'release_year' => 2008,
                'poster' => 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/hqkIcbrOHL86UncnHIsHVcVmzue.jpg',
                'video_url' => $sampleVideoUrl,
                'rating' => 9.0,
                'views' => 2000000,
                'is_featured' => true,
                'genres' => ['Action', 'Crime', 'Drama'],
                'actors' => ['Leonardo DiCaprio', 'Brad Pitt'],
                'directors' => ['Christopher Nolan'],
            ],
            [
                'title' => 'Pulp Fiction',
                'description' => 'The lives of two mob hitmen, a boxer, a gangster and his wife, and a pair of diner bandits intertwine in four tales of violence and redemption.',
                'duration' => 154,
                'release_year' => 1994,
                'poster' => 'https://image.tmdb.org/t/p/w500/d5iIlFn5s0ImszYzBPb8JPIfbXD.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/suaEOtk1N1sgg2MTM7oZd2cfVp3.jpg',
                'video_url' => $sampleVideoUrl,
                'rating' => 8.9,
                'views' => 1800000,
                'is_featured' => false,
                'genres' => ['Crime', 'Drama', 'Thriller'],
                'actors' => ['Brad Pitt', 'Ryan Gosling'],
                'directors' => ['Quentin Tarantino'],
            ],
            [
                'title' => 'The Matrix',
                'description' => 'A computer hacker learns from mysterious rebels about the true nature of his reality and his role in the war against its controllers.',
                'duration' => 136,
                'release_year' => 1999,
                'poster' => 'https://image.tmdb.org/t/p/w500/f89U3ADr1oiB1s9GkdPOEpXUk5H.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/fNG7i7RqMErkcqhohV2a6cV1Ehy.jpg',
                'video_url' => $sampleVideoUrl,
                'rating' => 8.7,
                'views' => 2200000,
                'is_featured' => true,
                'genres' => ['Action', 'Sci-Fi'],
                'actors' => ['Chris Evans', 'Scarlett Johansson'],
                'directors' => ['David Fincher'],
            ],
            [
                'title' => 'Interstellar',
                'description' => 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity\'s survival.',
                'duration' => 169,
                'release_year' => 2014,
                'poster' => 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/pbrkL804c8yAv3zBZR4QPKEJ20D.jpg',
                'video_url' => $sampleVideoUrl,
                'rating' => 8.6,
                'views' => 1700000,
                'is_featured' => false,
                'genres' => ['Adventure', 'Drama', 'Sci-Fi'],
                'actors' => ['Leonardo DiCaprio', 'Tom Hanks'],
                'directors' => ['Christopher Nolan'],
            ],
            [
                'title' => 'La La Land',
                'description' => 'While navigating their careers in Los Angeles, a pianist and an actress fall in love while attempting to reconcile their aspirations for the future.',
                'duration' => 128,
                'release_year' => 2016,
                'poster' => 'https://image.tmdb.org/t/p/w500/uDO8zWDhfWwoFdKS4fzkUJt0Rf0.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/6E84N4pz8XW7uBv7V7V8Z8Z8Z8Z.jpg',
                'video_url' => $sampleVideoUrl,
                'rating' => 8.0,
                'views' => 1200000,
                'is_featured' => false,
                'genres' => ['Comedy', 'Drama', 'Romance'],
                'actors' => ['Ryan Gosling', 'Emma Stone'],
                'directors' => ['Denis Villeneuve'],
            ],
            [
                'title' => 'The Shawshank Redemption',
                'description' => 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.',
                'duration' => 142,
                'release_year' => 1994,
                'poster' => 'https://image.tmdb.org/t/p/w500/q6y0Go1tsGEsmtFryDOJo3dEmqu.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/iNh3Biv0ygO8i1dJ1Zvq8Y5Z8Z8.jpg',
                'video_url' => $sampleVideoUrl,
                'rating' => 9.3,
                'views' => 2500000,
                'is_featured' => true,
                'genres' => ['Drama'],
                'actors' => ['Tom Hanks', 'Denzel Washington'],
                'directors' => ['Steven Spielberg'],
            ],
            [
                'title' => 'Fight Club',
                'description' => 'An insomniac office worker and a devil-may-care soapmaker form an underground fight club that evolves into something much, much more.',
                'duration' => 139,
                'release_year' => 1999,
                'poster' => 'https://image.tmdb.org/t/p/w500/pB8BM7pdSp6B6Ih7QZ4DrQ3PmJK.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/hZkgoQYus5vegHoetLkCJzb17zJ.jpg',
                'video_url' => $sampleVideoUrl,
                'rating' => 8.8,
                'views' => 1900000,
                'is_featured' => false,
                'genres' => ['Drama'],
                'actors' => ['Brad Pitt', 'Ryan Gosling'],
                'directors' => ['David Fincher'],
            ],
        ];

        foreach ($movies as $movieData) {
            $slug = Str::slug($movieData['title']);
            $movie = Movie::firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $movieData['title'],
                    'description' => $movieData['description'],
                    'duration' => $movieData['duration'],
                    'release_year' => $movieData['release_year'],
                    'poster' => $movieData['poster'],
                    'backdrop' => $movieData['backdrop'],
                    'video_url' => $movieData['video_url'],
                    'rating' => $movieData['rating'],
                    'views' => $movieData['views'],
                    'is_featured' => $movieData['is_featured'],
                ]
            );

            // Attach genres (sync to avoid duplicates)
            $genreIds = [];
            foreach ($movieData['genres'] as $genreName) {
                if (isset($genreModels[$genreName])) {
                    $genreIds[] = $genreModels[$genreName]->id;
                }
            }
            $movie->genres()->sync($genreIds);

            // Attach actors (sync to avoid duplicates)
            $actorIds = [];
            foreach ($movieData['actors'] as $actorName) {
                if (isset($actorModels[$actorName])) {
                    $actorIds[] = $actorModels[$actorName]->id;
                }
            }
            $movie->actors()->sync($actorIds);

            // Attach directors (sync to avoid duplicates)
            $directorIds = [];
            foreach ($movieData['directors'] as $directorName) {
                if (isset($directorModels[$directorName])) {
                    $directorIds[] = $directorModels[$directorName]->id;
                }
            }
            $movie->directors()->sync($directorIds);
        }

        // Create Series
        $series = [
            [
                'title' => 'Breaking Bad',
                'description' => 'A high school chemistry teacher turned methamphetamine manufacturer partners with a former student to secure his family\'s future.',
                'seasons' => 5,
                'release_year' => 2008,
                'poster' => 'https://image.tmdb.org/t/p/w500/ggFHVNu6YYI5L9pCfOacjizRGt.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/tsRy63Mu5cu8etL1X7ZLyf7UP1M.jpg',
                'video_url' => $sampleVideoUrl,
                'rating' => 9.5,
                'views' => 3000000,
                'is_featured' => true,
                'genres' => ['Crime', 'Drama', 'Thriller'],
                'actors' => ['Leonardo DiCaprio', 'Brad Pitt'],
                'directors' => ['David Fincher'],
            ],
            [
                'title' => 'Game of Thrones',
                'description' => 'Nine noble families fight for control over the lands of Westeros, while an ancient enemy returns after being dormant for millennia.',
                'seasons' => 8,
                'release_year' => 2011,
                'poster' => 'https://image.tmdb.org/t/p/w500/u3bZgnGQ9T01sWNhyveQz0wH0Hl.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/2OMB0ynKlyIenMJWI2Dy9IWT4cM.jpg',
                'video_url' => $sampleVideoUrl,
                'rating' => 9.2,
                'views' => 5000000,
                'is_featured' => true,
                'genres' => ['Action', 'Adventure', 'Drama', 'Fantasy'],
                'actors' => ['Chris Evans', 'Scarlett Johansson', 'Tom Hanks'],
                'directors' => ['Ridley Scott'],
            ],
            [
                'title' => 'Stranger Things',
                'description' => 'When a young boy vanishes, a small town uncovers a mystery involving secret experiments, terrifying supernatural forces and one strange little girl.',
                'seasons' => 4,
                'release_year' => 2016,
                'poster' => 'https://image.tmdb.org/t/p/w500/49WJfeN0moxb9IPfGn8AIqMGskD.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/56v2KjBlU4XaOv9rVf9yyz3bZ8Z.jpg',
                'video_url' => $sampleVideoUrl,
                'rating' => 8.7,
                'views' => 4000000,
                'is_featured' => true,
                'genres' => ['Drama', 'Fantasy', 'Horror', 'Sci-Fi'],
                'actors' => ['Emma Stone', 'Ryan Gosling'],
                'directors' => ['Denis Villeneuve'],
            ],
            [
                'title' => 'The Crown',
                'description' => 'Follows the political rivalries and romance of Queen Elizabeth II\'s reign and the events that shaped the second half of the 20th century.',
                'seasons' => 6,
                'release_year' => 2016,
                'poster' => 'https://image.tmdb.org/t/p/w500/1M876KPjulVwppEpldhdc8V4o68.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/1M876KPjulVwppEpldhdc8V4o68.jpg',
                'video_url' => $sampleVideoUrl,
                'rating' => 8.6,
                'views' => 2500000,
                'is_featured' => false,
                'genres' => ['Drama', 'History'],
                'actors' => ['Meryl Streep', 'Tom Hanks'],
                'directors' => ['Steven Spielberg'],
            ],
            [
                'title' => 'The Office',
                'description' => 'A mockumentary on a group of typical office workers, where the workday consists of ego clashes, inappropriate behavior, and tedium.',
                'seasons' => 9,
                'release_year' => 2005,
                'poster' => 'https://image.tmdb.org/t/p/w500/7DJKqBB5qY5ZqZqZqZqZqZqZqZq.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/7DJKqBB5qY5ZqZqZqZqZqZqZqZq.jpg',
                'video_url' => $sampleVideoUrl,
                'rating' => 8.9,
                'views' => 3500000,
                'is_featured' => false,
                'genres' => ['Comedy'],
                'actors' => ['Jennifer Lawrence', 'Chris Evans'],
                'directors' => ['Quentin Tarantino'],
            ],
            [
                'title' => 'The Mandalorian',
                'description' => 'The travels of a lone bounty hunter in the outer reaches of the galaxy, far from the authority of the New Republic.',
                'seasons' => 3,
                'release_year' => 2019,
                'poster' => 'https://image.tmdb.org/t/p/w500/sWgBv7LV2PRoQgkxwlibdGXKz1S.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/9ijMGlJKqcslswWUzTEwScms82f.jpg',
                'video_url' => $sampleVideoUrl,
                'rating' => 8.7,
                'views' => 2800000,
                'is_featured' => true,
                'genres' => ['Action', 'Adventure', 'Sci-Fi'],
                'actors' => ['Denzel Washington', 'Scarlett Johansson'],
                'directors' => ['James Cameron'],
            ],
        ];

        foreach ($series as $seriesData) {
            $slug = Str::slug($seriesData['title']);
            $seriesModel = Series::firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $seriesData['title'],
                    'description' => $seriesData['description'],
                    'seasons' => $seriesData['seasons'],
                    'release_year' => $seriesData['release_year'],
                    'poster' => $seriesData['poster'],
                    'backdrop' => $seriesData['backdrop'],
                    'video_url' => $seriesData['video_url'],
                    'rating' => $seriesData['rating'],
                    'views' => $seriesData['views'],
                    'is_featured' => $seriesData['is_featured'],
                ]
            );

            // Attach genres (sync to avoid duplicates)
            $genreIds = [];
            foreach ($seriesData['genres'] as $genreName) {
                if (isset($genreModels[$genreName])) {
                    $genreIds[] = $genreModels[$genreName]->id;
                }
            }
            $seriesModel->genres()->sync($genreIds);

            // Attach actors (sync to avoid duplicates)
            $actorIds = [];
            foreach ($seriesData['actors'] as $actorName) {
                if (isset($actorModels[$actorName])) {
                    $actorIds[] = $actorModels[$actorName]->id;
                }
            }
            $seriesModel->actors()->sync($actorIds);

            // Attach directors (sync to avoid duplicates)
            $directorIds = [];
            foreach ($seriesData['directors'] as $directorName) {
                if (isset($directorModels[$directorName])) {
                    $directorIds[] = $directorModels[$directorName]->id;
                }
            }
            $seriesModel->directors()->sync($directorIds);

            // Create episodes for each series (only if they don't exist)
            $episodesPerSeason = [10, 12, 8, 10, 13, 8]; // Varying episode counts
            for ($season = 1; $season <= $seriesModel->seasons; $season++) {
                $episodeCount = $episodesPerSeason[($season - 1) % count($episodesPerSeason)];
                
                for ($episode = 1; $episode <= $episodeCount; $episode++) {
                    Episode::firstOrCreate(
                        [
                            'series_id' => $seriesModel->id,
                            'season_number' => $season,
                            'episode_number' => $episode,
                        ],
                        [
                            'title' => "Episode {$episode}",
                            'description' => "In this episode, the story continues as our characters face new challenges and adventures.",
                            'duration' => rand(40, 60),
                            'video_url' => $sampleVideoUrl,
                            'thumbnail' => $seriesModel->poster,
                            'air_date' => now()->subDays(rand(1, 365)),
                        ]
                    );
                }
            }
        }

        $this->command->info('Movies, Series, Genres, Actors, Directors, and Episodes seeded successfully!');
    }
}

