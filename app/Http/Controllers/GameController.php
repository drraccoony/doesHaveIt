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
        if ($request->filled('app_id')) {
            $data = $request->validate([
                'app_id'    => 'required|integer|min:1',
                'game_name' => 'required|string|max:255',
            ]);

            $owns = $this->steam->ownsGame((int) $data['app_id']);

            return view('home', [
                'result'   => $owns,
                'gameName' => $data['game_name'],
            ]);
        }

        return view('home');
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
