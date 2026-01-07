<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Series;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
        $movies = Movie::where('title', 'LIKE', '%' . $search . '%')
            ->orderBy('release_year', 'desc')
            ->get();

        $series = Series::where('title', 'LIKE', '%' . $search . '%')
            ->orderBy('release_year', 'desc')
            ->get();

        return view('catalog.index', compact('movies', 'series', 'search'));
    }

        $featuredMovies = Movie::where('is_featured', true)
            ->orderBy('rating', 'desc')
            ->take(10)
            ->get();

        $featuredSeries = Series::where('is_featured', true)
            ->orderBy('rating', 'desc')
            ->take(10)
            ->get();

        $latestMovies = Movie::latest()->take(10)->get();
        $latestSeries = Series::latest()->take(10)->get();

        return view('catalog.index', compact('featuredMovies', 'featuredSeries', 'latestMovies', 'latestSeries'));
    }

    public function movies()
    {
        $movies = Movie::with(['genres'])
            ->orderBy('release_year', 'desc')
            ->paginate(24)
            ->withQueryString();

        $featuredMovies = Movie::where('is_featured', true)
            ->orderBy('rating', 'desc')
            ->take(5)
            ->get();

        return view('catalog.movies', compact('movies', 'featuredMovies'));
    }

    public function series()
    {
        $series = Series::with(['genres'])
            ->orderBy('release_year', 'desc')
            ->paginate(24)
            ->withQueryString();

        $featuredSeries = Series::where('is_featured', true)
            ->orderBy('rating', 'desc')
            ->take(5)
            ->get();

        return view('catalog.series', compact('series', 'featuredSeries'));
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        if (!$search) {
            return redirect()->route('catalog');
        }

        // 1. Recherche en BDD (Movies et Series)
        $movies = Movie::where('title', 'LIKE', '%' . $search . '%')
            ->orderBy('release_year', 'desc')
            ->take(20)
            ->get();

        $series = Series::where('title', 'LIKE', '%' . $search . '%')
            ->orderBy('release_year', 'desc')
            ->take(20)
            ->get();

        $totalResults = $movies->count() + $series->count();
        $externalResults = [];

        // 2. Si moins de 20 résultats, interroger TMDB (Multi search)
        if ($totalResults < 20) {
            try {
                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', 'https://api.themoviedb.org/3/search/multi?query=' . urlencode($search) . '&include_adult=false&language=fr-FR&page=1', [
                    'headers' => [
                        'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI2ZTQ1ZTVmOTEyNzhjYzQ3YTI3NzZlNjQ5ZTIzMmUwYiIsIm5iZiI6MTc2Nzc3ODEyOS42MTUsInN1YiI6IjY5NWUyNzUxNTQyODMxZmZkNmYyOTBkMCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.jc9FmMt_2xy_QMZP5sSZ6JFkD3ndqvnCkPPX2pWkljA',
                        'accept' => 'application/json',
                    ],
                ]);

                $data = json_decode($response->getBody(), true);
                $results = $data['results'] ?? [];

                foreach ($results as $item) {
                    $mediaType = $item['media_type'] ?? null;
                    if ($mediaType !== 'movie' && $mediaType !== 'tv') continue;

                    $title = $item['title'] ?? $item['name'] ?? null;
                    if (!$title) continue;

                    $slug = \Illuminate\Support\Str::slug($title);
                    $imageUrl = isset($item['poster_path']) ? 'https://image.tmdb.org/t/p/w500' . $item['poster_path'] : null;
                    $backdropUrl = isset($item['backdrop_path']) ? 'https://image.tmdb.org/t/p/original' . $item['backdrop_path'] : null;
                    $description = $item['overview'] ?? 'Pas de description.';
                    $date = $item['release_date'] ?? $item['first_air_date'] ?? date('Y-m-d');
                    $year = date('Y', strtotime($date));

                    if ($mediaType === 'movie') {
                        $movie = Movie::where('slug', $slug)->first();
                        if (!$movie) {
                            $movie = Movie::create([
                                'title' => $title,
                                'slug' => $slug,
                                'description' => $description,
                                'release_year' => $year,
                                'poster' => $imageUrl,
                                'backdrop' => $backdropUrl,
                                'duration' => 120,
                            ]);
                        }
                        $externalResults[] = $movie;
                    } else if ($mediaType === 'tv') {
                        $serie = Series::where('slug', $slug)->first();
                        if (!$serie) {
                            $serie = Series::create([
                                'title' => $title,
                                'slug' => $slug,
                                'description' => $description,
                                'release_year' => $year,
                                'seasons' => 1,
                                'poster' => $imageUrl,
                                'backdrop' => $backdropUrl,
                            ]);
                        }
                        $externalResults[] = $serie;
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('TMDB Multi-Search Error: ' . $e->getMessage());
            }
        }

        return view('catalog.search', compact('movies', 'series', 'externalResults', 'search'));
    }

}
