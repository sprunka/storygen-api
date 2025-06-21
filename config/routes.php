<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use StoryGen\Controllers\PromptController;
use StoryGen\Docs\Swagger;

/**
 * @OA\Info(
 *     title="Story Prompts API",
 *     version="1.0.0",
 *     description="A RESTful PHP API service that delivers randomized story prompts by combining elements—characters, settings, events, objects—based on a flexible JSON schema.",
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

/**
 * Routes configuration
 */
return function (App $app) {
    // Root endpoint
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
            // Get a complete story prompt
            $group->get('', PromptController::class . ':getPrompt');
            
            // Get a random card of a specific type
            $group->get('/cards', PromptController::class . ':getCard');
            
            // Get multiple random cards (simulating dice rolls)
            $group->get('/dice', PromptController::class . ':getDiceRolls');
        });
        
        // Components endpoint
        $group->get('/components', PromptController::class . ':getComponents');
    });
};