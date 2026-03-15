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
                'app_id'    => 'required|integer|min:1',
                'game_name' => 'required|string|max:255',
            ]);

            $appId = (int) $data['app_id'];
            $owns  = $this->steam->ownsGame($appId);

            return view('home', [
                'result'   => $owns,
                'gameName' => $data['game_name'],
                'debug'    => $debug,
                'debugInfo' => $debug ? [
                    'steam_id'  => config('steam.steam_id'),
                    'app_id'    => $appId,
                    'game_name' => $data['game_name'],
                    'result'    => $owns ? 'owns' : 'does not own',
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
