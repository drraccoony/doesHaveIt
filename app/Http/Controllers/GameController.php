<?php

namespace App\Http\Controllers;

use App\Services\SteamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameController extends Controller
{
    public function __construct(private SteamService $steam) {}

    public function index(): View
    {
        return view('home');
    }

    public function check(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'app_id'     => 'required|integer|min:1',
            'tiny_image' => 'nullable|string|max:500',
        ]);

        $appId = (int) $data['app_id'];

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

        return redirect()->route('game', $appId)->with('tiny_image', $tinyImage);
    }

    public function result(int $appId): View
    {
        $game = $this->steam->checkByAppId($appId);

        return view('home', [
            'result'    => $game['owned'],
            'gameName'  => $game['name'] ?? "App #{$appId}",
            'tinyImage' => session('tiny_image'),
        ]);
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
