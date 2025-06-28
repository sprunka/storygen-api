<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Story Prompts API",
 *     version="1.0.0",
 *     description="A RESTful PHP API service that delivers randomized story prompts by combining elements—characters,
 *                  settings, events, objects—based on a flexible JSON schema.",
 *     @OA\Contact(
 *         email="sprunka@gmail.com",
 *         name="Sean Prunka"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 */

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use StoryGen\Controllers\PromptController;
use StoryGen\Docs\Swagger;

/**
 * Routes configuration
 */
return function (App $app) {
    /**
     * @OA\Get(
     *     path="/",
     *     summary="Root endpoint that provides API information",
     *     description="Returns a message directing users to the API documentation",
     *     @OA\Response(
     *         response="200",
     *         description="Success response with documentation reference",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string")
     *         )
     *     )
     * )
     */
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(["Refer to the documentation at /openapi.json"], JSON_PRETTY_PRINT));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    });

    // OpenAPI docs
    /**
     * @OA\Get(
     *     path="/openapi.json",
     *     summary="Returns the OpenAPI 3.0 documentation in JSON format.",
     *     @OA\Response(
     *         response="200",
     *         description="The OpenAPI 3.0 documentation in JSON format. This documentation provides details on all
     * available API endpoints, including request parameters, response data, and response codes. The file is generated
     * dynamically based on the API documentation provided in the code base.",
     *         @OA\JsonContent(
     *             type="object"
     *         )
     *     )
     * )
     */
    $app->get('/openapi.json', Swagger::class)->setName('openApiDocs');

    // API endpoints
    $app->group('/api', function (Group $group) {
        // Prompts endpoints
        $group->group('/prompts', function (Group $group) {
            /**
             * @OA\Get(
             *     path="/api/prompts",
             *     summary="Get a complete story prompt",
             *     description="Generates and returns a complete story prompt combining various elements",
             *     @OA\Parameter(
             *         name="age_group",
             *         in="query",
             *         description="Target age group for the prompt",
             *         required=false,
             *         @OA\Schema(ref="#/components/schemas/AgeGroup")
             *     ),
             *     @OA\Parameter(
             *         name="count",
             *         in="query",
             *         description="Number of prompts to generate (1-10)",
             *         required=false,
             *         @OA\Schema(type="integer", minimum=1, maximum=10)
             *     ),
             *     @OA\Response(
             *         response="200",
             *         description="Successfully generated story prompt",
             *         @OA\JsonContent(type="object")
             *     ),
             *     @OA\Response(
             *         response="400",
             *         description="Invalid parameters",
             *         @OA\JsonContent(
             *             type="object",
             *             @OA\Property(property="error", type="string")
             *         )
             *     ),
             *     @OA\Response(
             *         response="500",
             *         description="Server error",
             *         @OA\JsonContent(
             *             type="object",
             *             @OA\Property(property="error", type="string")
             *         )
             *     )
             * )
             */
            $group->get('', PromptController::class . ':getPrompt');

            /**
             * @OA\Get(
             *     path="/api/prompts/cards",
             *     summary="Get a random card of a specific type",
             *     description="Returns a randomly selected card from the specified category",
             *     @OA\Parameter(
             *         name="type",
             *         in="query",
             *         description="The type of card to retrieve",
             *         required=true,
             *         @OA\Schema(type="string")
             *     ),
             *     @OA\Response(
             *         response="200",
             *         description="Successfully retrieved random card",
             *         @OA\JsonContent(type="object")
             *     ),
             *     @OA\Response(
             *         response="400",
             *         description="Missing or invalid type parameter",
             *         @OA\JsonContent(
             *             type="object",
             *             @OA\Property(property="error", type="string")
             *         )
             *     ),
             *     @OA\Response(
             *         response="500",
             *         description="Server error",
             *         @OA\JsonContent(
             *             type="object",
             *             @OA\Property(property="error", type="string")
             *         )
             *     )
             * )
             */
            $group->get('/cards', PromptController::class . ':getCard');

            /**
             * @OA\Get(
             *     path="/api/prompts/dice",
             *     summary="Get multiple random cards (simulating dice rolls)",
             *     description="Returns multiple randomly selected cards based on specified parameters",
             *     @OA\Parameter(
             *         name="count",
             *         in="query",
             *         description="Number of cards to retrieve (1-10)",
             *         required=false,
             *         @OA\Schema(type="integer", default=3, minimum=1, maximum=10)
             *     ),
             *     @OA\Response(
             *         response="200",
             *         description="Successfully retrieved random cards",
             *         @OA\JsonContent(
             *             type="array",
             *             @OA\Items(type="object")
             *         )
             *     ),
             *     @OA\Response(
             *         response="400",
             *         description="Invalid count parameter",
             *         @OA\JsonContent(
             *             type="object",
             *             @OA\Property(property="error", type="string")
             *         )
             *     ),
             *     @OA\Response(
             *         response="500",
             *         description="Server error",
             *         @OA\JsonContent(
             *             type="object",
             *             @OA\Property(property="error", type="string")
             *         )
             *     )
             * )
             */
            $group->get('/dice', PromptController::class . ':getDiceRolls');
        });

        /**
         * @OA\Get(
         *     path="/api/components",
         *     summary="Get available story components",
         *     description="Returns a list of story components filtered by the specified parameters",
         *     @OA\Parameter(
         *         name="category",
         *         in="query",
         *         description="Category of components to retrieve",
         *         required=true,
         *         @OA\Schema(ref="#/components/schemas/Category")
         *     ),
         *     @OA\Parameter(
         *         name="component_type",
         *         in="query",
         *         description="Type of component within the category",
         *         required=true,
         *         @OA\Schema(ref="#/components/schemas/ComponentType")
         *     ),
         *     @OA\Parameter(
         *         name="age_group",
         *         in="query",
         *         description="Target age group for components",
         *         required=false,
         *         @OA\Schema(ref="#/components/schemas/AgeGroup")
         *     ),
         *     @OA\Response(
         *         response="200",
         *         description="Successfully retrieved components",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(
         *                 property="components",
         *                 type="array",
         *                 @OA\Items(type="object")
         *             )
         *         )
         *     ),
         *     @OA\Response(
         *         response="400",
         *         description="Missing required parameters",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="error", type="string")
         *         )
         *     ),
         *     @OA\Response(
         *         response="500",
         *         description="Server error",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="error", type="string")
         *         )
         *     )
         * )
         */
        $group->get('/components', PromptController::class . ':getComponents');
    });
};

/**
 * @OA\Schema(
 *     schema="AgeGroup",
 *     type="string",
 *     enum={"kids", "teens", "adults"},
 *     description="Target age group for content filtering"
 * )
 */

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="string",
 *     enum={"characters", "settings", "events", "objects"},
 *     description="Category of story components"
 * )
 */

/**
 * @OA\Schema(
 *     schema="ComponentType",
 *     type="string",
 *     enum={"adjectives", "nouns", "verbs", "objects", "modifiers", "qualities", "items"},
 *     description="Type of component within a category"
 * )
 */
