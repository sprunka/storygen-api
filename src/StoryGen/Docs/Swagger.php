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
            $baseDir = __DIR__ . '/../../..';

            if (!is_dir($baseDir . '/config') || !is_dir($baseDir . '/src/StoryGen')) {
                throw new \RuntimeException('Required directories not found');
            }

            // Configure the OpenAPI generator with explicit settings
            $openapi = \OpenApi\scan([
                $baseDir . '/config',              // For routes.php and other config files
                $baseDir . '/src/StoryGen',        // For all StoryGen classes
            ], [
                'version' => '3.0.0',
            ]);

            if (!$openapi) {
                throw new \RuntimeException('Failed to generate OpenAPI documentation');
            }

            $json = $openapi->toJson();
            if (!$json) {
                throw new \RuntimeException('Failed to serialize OpenAPI documentation to JSON');
            }

            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write($json);
            return $response;
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Error generating OpenAPI documentation: ' . $e->getMessage(), [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            return $this->respondWithError($response, 'Error generating OpenAPI documentation: ' . $e->getMessage(), 500);
        }
    }
}
