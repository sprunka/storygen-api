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
     * Valid age groups for content filtering
     */
    private const VALID_AGE_GROUPS = ['kids', 'teens', 'adults'];

    /**
     * Valid categories for components
     */
    private const VALID_CATEGORIES = ['characters', 'settings', 'events', 'objects'];

    /**
     * Valid component types
     */
    private const VALID_COMPONENT_TYPES = ['adjectives', 'nouns', 'verbs', 'objects', 'modifiers', 'qualities', 'items'];

    /**
     * Valid component types per category
     */
    private const CATEGORY_COMPONENT_TYPES = [
        'characters' => ['adjectives', 'nouns'],
        'settings' => ['adjectives', 'nouns'],
        'events' => ['verbs', 'objects', 'modifiers'],
        'objects' => ['qualities', 'items']
    ];

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

        // Validate age group if provided
        if ($ageGroup !== null && !in_array($ageGroup, self::VALID_AGE_GROUPS)) {
            return $this->respondWithError(
                $response,
                'Invalid age_group. Must be one of: ' . implode(', ', self::VALID_AGE_GROUPS),
                400
            );
        }

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

        // Validate age group if provided
        if ($ageGroup !== null && !in_array($ageGroup, self::VALID_AGE_GROUPS)) {
            return $this->respondWithError(
                $response,
                'Invalid age_group. Must be one of: ' . implode(', ', self::VALID_AGE_GROUPS),
                400
            );
        }

        // Validate required parameters
        if (!$category) {
            return $this->respondWithError($response, 'Category parameter is required', 400);
        }

        if (!in_array($category, self::VALID_CATEGORIES)) {
            return $this->respondWithError(
                $response,
                'Invalid category. Must be one of: ' . implode(', ', self::VALID_CATEGORIES),
                400
            );
        }

        if (!$componentType) {
            return $this->respondWithError($response, 'Component type parameter is required', 400);
        }

        if (!in_array($componentType, self::VALID_COMPONENT_TYPES)) {
            return $this->respondWithError(
                $response,
                'Invalid component_type. Must be one of: ' . implode(', ', self::VALID_COMPONENT_TYPES),
                400
            );
        }

        // Validate that the component type is valid for the given category
        if (!in_array($componentType, self::CATEGORY_COMPONENT_TYPES[$category])) {
            return $this->respondWithError(
                $response,
                sprintf(
                    'Invalid component_type for category "%s". Must be one of: %s',
                    $category,
                    implode(', ', self::CATEGORY_COMPONENT_TYPES[$category])
                ),
                400
            );
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
     * Default invoke method that generates a complete story prompt
     * 
     * @param Request $request The request object
     * @param Response $response The response object
     * @param array $args Route arguments
     * @return Response The response with generated prompt
     */
    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        return $this->getPrompt($request, $response);
    }
}
