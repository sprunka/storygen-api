<?php

namespace App\Controllers;

use App\Services\PromptGenerator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @OA\Info(
 *     title="Story Prompts API",
 *     version="1.0.0",
 *     description="API for generating randomized story prompts"
 * )
 */
class PromptController
{
    /**
     * @var PromptGenerator
     */
    private PromptGenerator $promptGenerator;

    /**
     * Constructor
     *
     * @param PromptGenerator $promptGenerator
     */
    public function __construct(PromptGenerator $promptGenerator)
    {
        $this->promptGenerator = $promptGenerator;
    }

    /**
     * Generate a complete story prompt
     *
     * @OA\Get(
     *     path="/api/prompts",
     *     summary="Get a complete story prompt",
     *     description="Returns a full prompt with a Character + Setting + Event + Object schema. Each category can contain multiple elements generated from modular components.",
     *     @OA\Parameter(
     *         name="age_group",
     *         in="query",
     *         description="Filter by age group",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"kids", "teens", "adults", "any"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="count",
     *         in="query",
     *         description="Number of elements per category (default is random 1-3)",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             minimum=1,
     *             maximum=10
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="character",
     *                 type="array",
     *                 @OA\Items(type="string", example="shy librarian")
     *             ),
     *             @OA\Property(
     *                 property="setting",
     *                 type="array",
     *                 @OA\Items(type="string", example="abandoned amusement park")
     *             ),
     *             @OA\Property(
     *                 property="event",
     *                 type="array",
     *                 @OA\Items(type="string", example="finds a mysterious key")
     *             ),
     *             @OA\Property(
     *                 property="object",
     *                 type="array",
     *                 @OA\Items(type="string", example="antique locket")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid parameters"
     *     )
     * )
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getPrompt(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $ageGroup = $params['age_group'] ?? null;
        $count = isset($params['count']) ? (int) $params['count'] : null;

        // Validate count parameter if provided
        if ($count !== null && ($count < 1 || $count > 10)) {
            $response->getBody()->write(json_encode(['error' => 'Count must be between 1 and 10']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $prompt = $this->promptGenerator->generatePrompt($ageGroup, $count);
            $response->getBody()->write(json_encode($prompt));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'An unexpected error occurred']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Get a random card of a specific type
     *
     * @OA\Get(
     *     path="/api/prompts/cards",
     *     summary="Get a random card of a specific type",
     *     description="Returns a single-element card. The element is generated from modular components.",
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Card type",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"character", "setting", "event", "object"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="tableTitle", type="string", example="Character Card"),
     *             @OA\Property(property="character", type="string", example="shy librarian", description="Present only when type=character"),
     *             @OA\Property(property="setting", type="string", example="abandoned amusement park", description="Present only when type=setting"),
     *             @OA\Property(property="event", type="string", example="finds a mysterious key", description="Present only when type=event"),
     *             @OA\Property(property="object", type="string", example="antique locket", description="Present only when type=object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid type parameter"
     *     )
     * )
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getCard(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $type = $params['type'] ?? null;

        if (!$type) {
            $response->getBody()->write(json_encode(['error' => 'Type parameter is required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $card = $this->promptGenerator->getRandomCard($type);
            $response->getBody()->write(json_encode($card));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'An unexpected error occurred']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Generate multiple random cards (simulating dice rolls)
     *
     * @OA\Get(
     *     path="/api/prompts/dice",
     *     summary="Generate multiple random cards",
     *     description="Returns cards as an object to simulate dice rolls. Each card is generated from modular components.",
     *     @OA\Parameter(
     *         name="count",
     *         in="query",
     *         description="Number of cards to generate",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=3
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="tableTitle", type="string", example="Dice Rolls"),
     *             @OA\Property(
     *                 property="cards",
     *                 type="object",
     *                 @OA\Property(property="character", type="string", example="shy librarian"),
     *                 @OA\Property(property="setting", type="string", example="abandoned amusement park"),
     *                 @OA\Property(property="event", type="string", example="finds a mysterious key")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid count parameter"
     *     )
     * )
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getDiceRolls(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $count = isset($params['count']) ? (int) $params['count'] : 3;

        if ($count < 1 || $count > 10) {
            $response->getBody()->write(json_encode(['error' => 'Count must be between 1 and 10']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $rolls = $this->promptGenerator->generateDiceRolls($count);
            $response->getBody()->write(json_encode($rolls));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'An unexpected error occurred']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Get individual components by type
     *
     * @OA\Get(
     *     path="/api/components",
     *     summary="Get individual components by type",
     *     description="Returns an array of individual components (adjectives, nouns, verbs, etc.) for a specific category",
     *     @OA\Parameter(
     *         name="age_group",
     *         in="query",
     *         description="Filter by age group",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"kids", "teens", "adults", "any"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Category of components",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"characters", "settings", "events", "objects"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="component_type",
     *         in="query",
     *         description="Type of component",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"adjectives", "nouns", "verbs", "objects", "modifiers", "qualities", "items"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="components",
     *                 type="array",
     *                 @OA\Items(type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid parameters"
     *     )
     * )
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getComponents(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $ageGroup = $params['age_group'] ?? null;
        $category = $params['category'] ?? null;
        $componentType = $params['component_type'] ?? null;

        // Validate required parameters
        if (!$category) {
            $response->getBody()->write(json_encode(['error' => 'Category parameter is required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if (!$componentType) {
            $response->getBody()->write(json_encode(['error' => 'Component type parameter is required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            // Get the SeedLoader from the PromptGenerator
            $seedLoader = $this->getSeedLoader();

            // Get the components
            $components = $seedLoader->getModularComponents($ageGroup, $category, $componentType);

            // Return the components
            $response->getBody()->write(json_encode(['components' => $components]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'An unexpected error occurred']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Get the SeedLoader from the PromptGenerator
     *
     * @return \App\Services\SeedLoader
     */
    private function getSeedLoader(): \App\Services\SeedLoader
    {
        // Use reflection to get the SeedLoader from the PromptGenerator
        $reflection = new \ReflectionClass($this->promptGenerator);
        $property = $reflection->getProperty('seedLoader');
        $property->setAccessible(true);
        return $property->getValue($this->promptGenerator);
    }
}
