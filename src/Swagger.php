<?php

namespace App;

use OpenApi\Generator;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Swagger documentation generator
 */
class Swagger
{
    /**
     * Generate OpenAPI documentation
     *
     * @param Request $request The request
     * @param Response $response The response
     * @return Response The response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        // Configure the OpenAPI generator with explicit settings
        $openapi = Generator::scan([
            // Explicitly include the OpenApi.php file first to ensure it's processed before other files
            __DIR__ . '/OpenApi.php',
            // Include the Controllers directory for endpoint annotations
            __DIR__ . '/Controllers',
            // Include the entire src directory to catch any other annotations
            __DIR__,
        ], [
            'version' => '3.0.0', // Explicitly set the OpenAPI version
        ]);

        // Return the OpenAPI specification as JSON
        $response->getBody()->write($openapi->toJson());

        return $response->withHeader('Content-Type', 'application/json');
    }
}
