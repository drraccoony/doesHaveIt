<?php

namespace App\Http\Controllers;

use App\Services\SteamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameController extends Controller
{
    public function __construct(private SteamService $steam) {}

    public function index(Request $request): View
    {
        $debug = $request->has('debug');

        if ($request->filled('app_id')) {
            $data = $request->validate([
                'app_id'     => 'required|integer|min:1',
                'game_name'  => 'required|string|max:255',
                'tiny_image' => 'nullable|string|max:500',
            ]);

            $appId = (int) $data['app_id'];
            $owns  = $this->steam->ownsGame($appId);

            // Only trust tiny_image URLs from known Steam CDN hosts
            $tinyImage = null;
            if (!empty($data['tiny_image'])) {
                $host = parse_url($data['tiny_image'], PHP_URL_HOST);
                if ($host !== false && $host !== null && (
                    str_ends_with($host, '.steampowered.com') ||
                    str_ends_with($host, '.steamstatic.com')
                )) {
                    $tinyImage = $data['tiny_image'];
                }
            }

            return view('home', [
                'result'     => $owns,
                'gameName'   => $data['game_name'],
                'tinyImage'  => $tinyImage,
                'debug'    => $debug,
                'debugInfo' => $debug ? [
                    'steam_id'   => config('steam.steam_id'),
                    'app_id'     => $appId,
                    'game_name'  => $data['game_name'],
                    'tiny_image' => $tinyImage ?? '(none)',
                    'result'     => $owns ? 'owns' : 'does not own',
                    'queried_at' => now()->toDateTimeString(),
                ] : null,
            ]);
        }

        return view('home', ['debug' => $debug]);
    }

    public function autocomplete(Request $request): JsonResponse
    {
        $query = $request->validate(['q' => 'required|string|min:2|max:100'])['q'];

        return response()->json($this->steam->searchGames($query));
    }

    public function recent(): View
    {
        return view('recent', [
            'games' => $this->steam->getRecentlyPlayedGames(5),
        ]);
    }
}
