<?php

namespace StoryGen\Docs;

use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use StoryGen\AbstractController;

/**
 * Swagger documentation generator
 */
class Swagger extends AbstractController
{
    /**
     * Generate OpenAPI documentation
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response The response
     */
    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        try {
            // Configure the OpenAPI generator with explicit settings
            $openapi = \OpenApi\scan([
                // Include the config/routes.php file for endpoint annotations
                __DIR__ . '/../../../config/routes.php',
                // Include only the StoryGen directory for annotations
                __DIR__ . '/../',
            ], [
                'version' => '3.0.0', // Explicitly set the OpenAPI version
            ]);

            // Return the OpenAPI specification as JSON
            return $this->respondWithJson($response, $openapi);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Error generating OpenAPI documentation: ' . $e->getMessage());
            }
            return $this->respondWithError($response, 'Error generating OpenAPI documentation', 500);
        }
    }
}
