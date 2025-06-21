<?php

namespace App;

use OpenApi\Annotations\OpenApi;
use OpenApi\Generator;
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
        $openapi = Generator::scan([__DIR__]);
        $response->getBody()->write($openapi->toJson());

        return $response->withHeader('Content-Type', 'application/json');
    }
}