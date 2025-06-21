<?php

use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;
use App\Controllers\PromptController;
use App\Services\PromptGenerator;
use App\Services\SeedLoader;
use App\Swagger;

require_once __DIR__ . '/../vendor/autoload.php';

// Set up dependency injection container
$containerBuilder = new ContainerBuilder();

// Add definitions to the container
$containerBuilder->addDefinitions([
    SeedLoader::class => function () {
        return new SeedLoader();
    },
    PromptGenerator::class => function ($container) {
        return new PromptGenerator($container->get(SeedLoader::class));
    },
    PromptController::class => function ($container) {
        return new PromptController(
            $container->get(PromptGenerator::class)
        );
    }
]);

// Build the container
$container = $containerBuilder->build();

// Create the app with the container
AppFactory::setContainer($container);
$app = AppFactory::create();

// Add middleware for base path
$app->add(new BasePathMiddleware($app));

// Add error middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Define routes
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode(["Refer to the documentation at /openapi.json"], JSON_PRETTY_PRINT));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

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

$app->get('/api/prompts', function (Request $request, Response $response) use ($container) {
    return $container->get(PromptController::class)->getPrompt($request, $response);
});

$app->get('/api/prompts/cards', function (Request $request, Response $response) use ($container) {
    return $container->get(PromptController::class)->getCard($request, $response);
});

$app->get('/api/prompts/dice', function (Request $request, Response $response) use ($container) {
    return $container->get(PromptController::class)->getDiceRolls($request, $response);
});


// Run the app
$app->run();
