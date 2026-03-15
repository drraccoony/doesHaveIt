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

        return Cache::remember($cacheKey, 1, function () use ($query) {
            $response = Http::timeout(5)->get('https://store.steampowered.com/api/storesearch/', [
                'term' => $query,
                'l'    => 'english',
                'cc'   => 'US',
            ]);

            if ($response->failed()) {
                return [];
            }

            \Log::debug('Steam search response', ['query' => $query, 'response' => $response->json()]);

            return array_map(fn ($item) => [
                'id'         => $item['id'],
                'name'       => $item['name'],
                'type'       => strtolower($item['type'] ?? 'app'),
                'tiny_image' => $item['tiny_image'] ?? null,
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

    public function checkByAppId(int $appId): array
    {
        $name = Cache::remember('steam_appdetails_' . $appId, 3600, function () use ($appId) {
            $response = Http::timeout(5)->get('https://store.steampowered.com/api/appdetails', [
                'appids' => $appId,
            ]);

            if ($response->failed()) {
                return null;
            }

            $data = $response->json((string) $appId);

            return ($data['success'] ?? false) ? ($data['data']['name'] ?? null) : null;
        });

        return [
            'app_id' => $appId,
            'name'   => $name,
            'owned'  => $this->ownsGame($appId),
        ];
    }

    public function checkByTerm(string $term): array
    {
        $results = $this->searchGames($term);

        if (empty($results)) {
            return [
                'term'  => $term,
                'app_id' => null,
                'name'  => null,
                'owned' => false,
                'found' => false,
            ];
        }

        $normalized = strtolower(trim($term));

        // Prefer exact match, then starts-with, then first result
        $match = null;
        foreach ($results as $game) {
            if (strtolower($game['name']) === $normalized) {
                $match = $game;
                break;
            }
        }

        if ($match === null) {
            foreach ($results as $game) {
                if (str_starts_with(strtolower($game['name']), $normalized)) {
                    $match = $game;
                    break;
                }
            }
        }

        $match ??= $results[0];

        return [
            'term'   => $term,
            'app_id' => $match['id'],
            'name'   => $match['name'],
            'owned'  => $this->ownsGame($match['id']),
            'found'  => true,
        ];
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
