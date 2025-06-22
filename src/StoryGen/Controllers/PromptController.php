<?php

namespace StoryGen\Controllers;

use StoryGen\AbstractController;
use StoryGen\Services\PromptGenerator;
use StoryGen\Services\SeedLoader;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

/**
 * Controller for story prompt generation endpoints
 */
class PromptController extends AbstractController
{
    /**
     * @var PromptGenerator
     */
    private PromptGenerator $promptGenerator;

    /**
     * Constructor
     *
     * @param PromptGenerator $promptGenerator
     * @param LoggerInterface|null $logger
     */
    public function __construct(PromptGenerator $promptGenerator, ?LoggerInterface $logger = null)
    {
        parent::__construct($logger);
        $this->promptGenerator = $promptGenerator;
    }

    /**
     * Generate a complete story prompt
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
            return $this->respondWithError($response, 'Count must be between 1 and 10', 400);
        }

        try {
            $prompt = $this->promptGenerator->generatePrompt($ageGroup, $count);
            return $this->respondWithJson($response, $prompt);
        } catch (\InvalidArgumentException $e) {
            return $this->respondWithError($response, $e->getMessage(), 400);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Error generating prompt: ' . $e->getMessage());
            }
            return $this->respondWithError($response, 'An unexpected error occurred', 500);
        }
    }

    /**
     * Get a random card of a specific type
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
            return $this->respondWithError($response, 'Type parameter is required', 400);
        }

        try {
            $card = $this->promptGenerator->getRandomCard($type);
            return $this->respondWithJson($response, $card);
        } catch (\InvalidArgumentException $e) {
            return $this->respondWithError($response, $e->getMessage(), 400);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Error generating card: ' . $e->getMessage());
            }
            return $this->respondWithError($response, 'An unexpected error occurred', 500);
        }
    }

    /**
     * Generate multiple random cards (simulating dice rolls)
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
            return $this->respondWithError($response, 'Count must be between 1 and 10', 400);
        }

        try {
            $rolls = $this->promptGenerator->generateDiceRolls($count);
            return $this->respondWithJson($response, $rolls);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Error generating dice rolls: ' . $e->getMessage());
            }
            return $this->respondWithError($response, 'An unexpected error occurred', 500);
        }
    }

    /**
     * Get individual components by type
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
            return $this->respondWithError($response, 'Category parameter is required', 400);
        }

        if (!$componentType) {
            return $this->respondWithError($response, 'Component type parameter is required', 400);
        }

        try {
            // Get the SeedLoader from the PromptGenerator
            $seedLoader = $this->getSeedLoader();

            // Get the components
            $components = $seedLoader->getModularComponents($ageGroup, $category, $componentType);

            // Return the components
            return $this->respondWithJson($response, ['components' => $components]);
        } catch (\InvalidArgumentException $e) {
            return $this->respondWithError($response, $e->getMessage(), 400);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Error getting components: ' . $e->getMessage());
            }
            return $this->respondWithError($response, 'An unexpected error occurred', 500);
        }
    }

    /**
     * Get the SeedLoader from the PromptGenerator
     *
     * @return SeedLoader
     */
    private function getSeedLoader(): SeedLoader
    {
        // Use reflection to get the SeedLoader from the PromptGenerator
        $reflection = new \ReflectionClass($this->promptGenerator);
        $property = $reflection->getProperty('seedLoader');
        $property->setAccessible(true);
        return $property->getValue($this->promptGenerator);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        return $this->getPrompt($request, $response);
    }
}
