<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SteamService
{
    private string $apiKey;
    private string $steamId;

    public function __construct()
    {
        $this->apiKey = config('steam.api_key');
        $this->steamId = config('steam.steam_id');
    }

    public function searchGames(string $query): array
    {
        $cacheKey = 'steam_search_' . md5(strtolower(trim($query)));

        return Cache::remember($cacheKey, 3600, function () use ($query) {
            $response = Http::timeout(5)->get('https://store.steampowered.com/api/storesearch/', [
                'term' => $query,
                'l'    => 'english',
                'cc'   => 'US',
            ]);

            if ($response->failed()) {
                return [];
            }

            return array_map(fn ($item) => [
                'id'   => $item['id'],
                'name' => $item['name'],
            ], $response->json('items') ?? []);
        });
    }

    public function ownsGame(int $appId): bool
    {
        return in_array($appId, $this->getOwnedAppIds());
    }

    public function getRecentlyPlayedGames(int $limit = 5): array
    {
        return Cache::remember('steam_recent_played_' . $this->steamId, 300, function () use ($limit) {
            $response = Http::timeout(10)->get('https://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/', [
                'key'     => $this->apiKey,
                'steamid' => $this->steamId,
                'count'   => $limit,
                'format'  => 'json',
            ]);

            if ($response->failed()) {
                return [];
            }

            return array_map(fn ($game) => [
                'appid'            => $game['appid'],
                'name'             => $game['name'],
                'playtime_forever' => $game['playtime_forever'],
                'playtime_2weeks'  => $game['playtime_2weeks'] ?? 0,
            ], array_slice($response->json('response.games') ?? [], 0, $limit));
        });
    }

    private function getOwnedAppIds(): array
    {
        return Cache::remember('steam_owned_games_' . $this->steamId, 300, function () {
            $response = Http::timeout(10)->get('https://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/', [
                'key'                    => $this->apiKey,
                'steamid'                => $this->steamId,
                'include_appinfo'        => false,
                'include_played_free_games' => true,
                'format'                 => 'json',
            ]);

            if ($response->failed()) {
                return [];
            }

            $games = $response->json('response.games') ?? [];

            return array_column($games, 'appid');
        });
    }
}
