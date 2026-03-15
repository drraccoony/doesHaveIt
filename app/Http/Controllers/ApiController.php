<?php

namespace App\Http\Controllers;

use App\Services\SteamService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'DoesValterHaveIt? API',
    description: 'Check whether a Steam game is owned specifically by Valter (and ONLY Valter) by looking up an App ID or searching by name.',
)]
#[OA\Server(url: '/api', description: 'API base')]
class ApiController extends Controller
{
    public function __construct(private SteamService $steam) {}

    #[OA\Get(
        path: '/check/appid/{appId}',
        summary: 'Check ownership by Steam App ID',
        tags: ['Games'],
        parameters: [
            new OA\Parameter(
                name: 'appId',
                in: 'path',
                required: true,
                description: 'Steam numeric App ID (e.g. 570 for Dota 2)',
                schema: new OA\Schema(type: 'integer', example: 570),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Ownership result',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'app_id', type: 'integer', example: 570),
                        new OA\Property(property: 'name',   type: 'string',  example: 'Dota 2', nullable: true),
                        new OA\Property(property: 'owned',  type: 'boolean', example: true),
                    ],
                ),
            ),
            new OA\Response(
                response: 429,
                description: 'Rate limit exceeded (30 requests per minute)',
            ),
        ],
    )]
    public function checkByAppId(int $appId): JsonResponse
    {
        if ($appId < 1) {
            return response()->json(['error' => 'Invalid app ID.'], 422);
        }

        return response()->json($this->steam->checkByAppId($appId));
    }

    #[OA\Get(
        path: '/check/search/{term}',
        summary: 'Check ownership by search term',
        description: 'Searches Steam for the best matching game and checks if it is owned. Prefers exact name matches, then prefix matches, then the top result.',
        tags: ['Games'],
        parameters: [
            new OA\Parameter(
                name: 'term',
                in: 'path',
                required: true,
                description: 'Game name to search for (2–100 characters)',
                schema: new OA\Schema(type: 'string', example: 'Halo'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Best-match ownership result',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'term',   type: 'string',  example: 'Halo'),
                        new OA\Property(property: 'app_id', type: 'integer', example: 976730, nullable: true),
                        new OA\Property(property: 'name',   type: 'string',  example: 'Halo: The Master Chief Collection', nullable: true),
                        new OA\Property(property: 'owned',  type: 'boolean', example: false),
                        new OA\Property(property: 'found',  type: 'boolean', example: true),
                    ],
                ),
            ),
            new OA\Response(
                response: 422,
                description: 'Term too short or too long',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Term must be between 2 and 100 characters.'),
                    ],
                ),
            ),
            new OA\Response(
                response: 429,
                description: 'Rate limit exceeded (30 requests per minute)',
            ),
        ],
    )]
    public function checkByTerm(string $term): JsonResponse
    {
        $term = trim($term);

        if (strlen($term) < 2 || strlen($term) > 100) {
            return response()->json(['error' => 'Term must be between 2 and 100 characters.'], 422);
        }

        return response()->json($this->steam->checkByTerm($term));
    }
}
