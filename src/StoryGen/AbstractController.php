<?php

namespace StoryGen;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;


/**
 * Class AbstractRoute
 * @package StoryGen
 */
abstract class AbstractController
{
    /**
     * @var LoggerInterface|null
     */
    protected ?LoggerInterface $logger;

    /**
     * @var array
     */
    protected array $help = [];
    /**
     * Constructor
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @return object
     */
    public function getHelp()
    {
        return (object)$this->help;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    abstract public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface;

    /**
     * @param Response $response
     * @param array $outArray
     * @return Response
     */
    protected function outputResponse(Response $response, array $outArray) : Response
    {
        $response->getBody()->write(json_encode($outArray, JSON_PRETTY_PRINT));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
    /**
     * Format and return a JSON response
     *
     * @param Response $response The response object
     * @param mixed $data The data to return
     * @param int $status The HTTP status code
     * @return Response The formatted response
     */
    protected function respondWithJson(Response $response, $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    /**
     * Format and return an error response
     *
     * @param Response $response The response object
     * @param string $message The error message
     * @param int $status The HTTP status code
     * @return Response The formatted error response
     */
    protected function respondWithError(Response $response, string $message, int $status = 400): Response
    {
        return $this->respondWithJson($response, ['error' => $message], $status);
    }

    /**
     * Get query parameters from request
     *
     * @param Request $request The request object
     * @param array $required Required parameters
     * @param array $optional Optional parameters with default values
     * @return array|Response Parameters or error response
     */
    protected function getParams(Request $request, array $required = [], array $optional = [])
    {
        $params = $request->getQueryParams();
        $result = [];

        // Check required parameters
        foreach ($required as $param) {
            if (!isset($params[$param]) || $params[$param] === '') {
                return ['error' => "Parameter '$param' is required", 'status' => 400];
            }
            $result[$param] = $params[$param];
        }

        // Add optional parameters with defaults
        foreach ($optional as $param => $default) {
            $result[$param] = $params[$param] ?? $default;
        }

        return $result;
    }

    /**
     * Validate parameters
     *
     * @param array $params Parameters to validate
     * @param array $rules Validation rules
     * @return array|null Error message or null if valid
     */
    protected function validateParams(array $params, array $rules): ?array
    {
        foreach ($rules as $param => $rule) {
            if (!isset($params[$param])) {
                continue;
            }

            $value = $params[$param];

            if (isset($rule['type'])) {
                switch ($rule['type']) {
                    case 'int':
                        if (!is_numeric($value)) {
                            return ['error' => "Parameter '$param' must be a number", 'status' => 400];
                        }
                        $params[$param] = (int) $value;
                        break;
                    case 'string':
                        if (!is_string($value)) {
                            return ['error' => "Parameter '$param' must be a string", 'status' => 400];
                        }
                        break;
                }
            }

            if (isset($rule['min']) && $value < $rule['min']) {
                return ['error' => "Parameter '$param' must be at least {$rule['min']}", 'status' => 400];
            }

            if (isset($rule['max']) && $value > $rule['max']) {
                return ['error' => "Parameter '$param' must be at most {$rule['max']}", 'status' => 400];
            }

            if (isset($rule['enum']) && !in_array($value, $rule['enum'])) {
                $allowed = implode(', ', $rule['enum']);
                return ['error' => "Parameter '$param' must be one of: $allowed", 'status' => 400];
            }
        }

        return null;
    }
}