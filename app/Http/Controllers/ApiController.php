<?php

namespace App\Http\Controllers;

use App\Services\SteamService;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    public function __construct(private SteamService $steam) {}

    public function checkByAppId(int $appId): JsonResponse
    {
        if ($appId < 1) {
            return response()->json(['error' => 'Invalid app ID.'], 422);
        }

        return response()->json($this->steam->checkByAppId($appId));
    }

    public function checkByTerm(string $term): JsonResponse
    {
        $term = trim($term);

        if (strlen($term) < 2 || strlen($term) > 100) {
            return response()->json(['error' => 'Term must be between 2 and 100 characters.'], 422);
        }

        return response()->json($this->steam->checkByTerm($term));
    }
}
