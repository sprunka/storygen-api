<?php

namespace StoryGen\Services;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Prompt",
 *     description="A complete story prompt",
 *     @OA\Property(property="character", type="string", example="shy librarian"),
 *     @OA\Property(property="setting", type="string", example="abandoned amusement park"),
 *     @OA\Property(property="event", type="string", example="finds a mysterious key"),
 *     @OA\Property(property="object", type="string", example="antique locket")
 * )
 */
class PromptGenerator
{
    /**
     * @var SeedLoader
     */
    private SeedLoader $seedLoader;

    /**
     * @var ComponentCombiner|null
     */
    private ?ComponentCombiner $componentCombiner;

    /**
     * Constructor
     *
     * @param SeedLoader $seedLoader
     * @param ComponentCombiner|null $componentCombiner
     */
    public function __construct(SeedLoader $seedLoader, ?ComponentCombiner $componentCombiner = null)
    {
        $this->seedLoader = $seedLoader;
        $this->componentCombiner = $componentCombiner;
    }

    /**
     * Generate a complete story prompt
     *
     * @param string|null $ageGroup Age group (kids, teens, adults)
     * @param int|null $count Number of elements per category (null for random 1-3)
     * @return array Complete story prompt with character, setting, event, and object
     */
    public function generatePrompt(?string $ageGroup = null, ?int $count = null): array
    {
        // If count is null, randomize between 1 and 3
        if ($count === null) {
            $count = random_int(1, 3);
        }

        // If using component combiner, generate prompt using modular components
        if ($this->componentCombiner !== null) {
            return $this->generateModularPrompt($ageGroup, $count);
        }

        // Otherwise, use the original method
        $elements = $this->seedLoader->getPromptElementsByAgeGroup($ageGroup);

        $prompt = [];

        // Generate multiple elements for each category
        $prompt['character'] = $this->getRandomElements($elements['characters'] ?? [], $count);
        $prompt['setting'] = $this->getRandomElements($elements['settings'] ?? [], $count);
        $prompt['event'] = $this->getRandomElements($elements['events'] ?? [], $count);
        $prompt['object'] = $this->getRandomElements($elements['objects'] ?? [], $count);

        return $prompt;
    }

    /**
     * Generate a complete story prompt using modular components
     *
     * @param string|null $ageGroup Age group (kids, teens, adults)
     * @param int $count Number of elements per category
     * @return array Complete story prompt with character, setting, event, and object
     */
    private function generateModularPrompt(?string $ageGroup = null, int $count = 1): array
    {
        $prompt = [];

        // Generate characters
        $characterComponents = [
            'adjectives' => $this->seedLoader->getModularComponents($ageGroup, 'characters', 'adjectives'),
            'nouns' => $this->seedLoader->getModularComponents($ageGroup, 'characters', 'nouns')
        ];
        $prompt['character'] = $this->componentCombiner->generateRandomElements(
            'character',
            $characterComponents,
            $count
        );

        // Generate settings
        $settingComponents = [
            'adjectives' => $this->seedLoader->getModularComponents($ageGroup, 'settings', 'adjectives'),
            'nouns' => $this->seedLoader->getModularComponents($ageGroup, 'settings', 'nouns')
        ];
        $prompt['setting'] = $this->componentCombiner->generateRandomElements(
            'setting',
            $settingComponents,
            $count
        );

        // Generate events
        $eventComponents = [
            'verbs' => $this->seedLoader->getModularComponents($ageGroup, 'events', 'verbs'),
            'objects' => $this->seedLoader->getModularComponents($ageGroup, 'events', 'objects'),
            'modifiers' => $this->seedLoader->getModularComponents($ageGroup, 'events', 'modifiers')
        ];
        $prompt['event'] = $this->componentCombiner->generateRandomElements(
            'event',
            $eventComponents,
            $count
        );

        // Generate objects
        $objectComponents = [
            'qualities' => $this->seedLoader->getModularComponents($ageGroup, 'objects', 'qualities'),
            'items' => $this->seedLoader->getModularComponents($ageGroup, 'objects', 'items')
        ];
        $prompt['object'] = $this->componentCombiner->generateRandomElements(
            'object',
            $objectComponents,
            $count
        );

        return $prompt;
    }

    /**
     * Get a random card of a specific type
     *
     * @param string $type Element type (character, setting, event, object)
     * @return array Card with tableTitle and the specific type value
     */
    public function getRandomCard(string $type): array
    {
        // If using component combiner, generate card using modular components
        if ($this->componentCombiner !== null) {
            return $this->getRandomModularCard($type);
        }

        // Convert singular to plural for the seed data structure
        $pluralType = $this->singularToPlural($type);

        // Get all elements of the specified type
        $elements = $this->seedLoader->getAllElementsByType($pluralType);

        return [
            'tableTitle' => ucfirst($type) . ' Card',
            $type => $this->getRandomElement($elements)
        ];
    }

    /**
     * Get a random card of a specific type using modular components
     *
     * @param string $type Element type (character, setting, event, object)
     * @return array Card with tableTitle and the specific type value
     */
    private function getRandomModularCard(string $type): array
    {
        $element = null;

        switch ($type) {
            case 'character':
                $components = [
                    'adjectives' => $this->seedLoader->getModularComponents(null, 'characters', 'adjectives'),
                    'nouns' => $this->seedLoader->getModularComponents(null, 'characters', 'nouns')
                ];
                $element = $this->componentCombiner->generateRandomCharacter(
                    $components['adjectives'],
                    $components['nouns']
                );
                break;
            case 'setting':
                $components = [
                    'adjectives' => $this->seedLoader->getModularComponents(null, 'settings', 'adjectives'),
                    'nouns' => $this->seedLoader->getModularComponents(null, 'settings', 'nouns')
                ];
                $element = $this->componentCombiner->generateRandomSetting(
                    $components['adjectives'],
                    $components['nouns']
                );
                break;
            case 'event':
                $components = [
                    'verbs' => $this->seedLoader->getModularComponents(null, 'events', 'verbs'),
                    'objects' => $this->seedLoader->getModularComponents(null, 'events', 'objects'),
                    'modifiers' => $this->seedLoader->getModularComponents(null, 'events', 'modifiers')
                ];
                $element = $this->componentCombiner->generateRandomEvent(
                    $components['verbs'],
                    $components['objects'],
                    $components['modifiers']
                );
                break;
            case 'object':
                $components = [
                    'qualities' => $this->seedLoader->getModularComponents(null, 'objects', 'qualities'),
                    'items' => $this->seedLoader->getModularComponents(null, 'objects', 'items')
                ];
                $element = $this->componentCombiner->generateRandomObject(
                    $components['qualities'],
                    $components['items']
                );
                break;
        }

        return [
            'tableTitle' => ucfirst($type) . ' Card',
            $type => $element
        ];
    }

    /**
     * Generate multiple random cards (simulating dice rolls)
     *
     * @param int $count Number of cards to generate
     * @return array Array with tableTitle and cards as an object
     */
    public function generateDiceRolls(int $count): array
    {
        $types = ['characters', 'settings', 'events', 'objects'];
        $cards = [];

        // Ensure we don't try to generate more cards than we have types
        $count = min($count, count($types));

        // Shuffle the types array to get random types without duplicates
        shuffle($types);

        // Take the first $count types
        $selectedTypes = array_slice($types, 0, $count);

        // Generate a card for each selected type
        foreach ($selectedTypes as $type) {
            $singularType = rtrim($type, 's');

            // If using component combiner, generate card using modular components
            if ($this->componentCombiner !== null) {
                $card = $this->getRandomModularCard($singularType);
                $cards[$singularType] = $card[$singularType];
            } else {
                // Get all elements of that type
                $elements = $this->seedLoader->getAllElementsByType($type);

                // Pick a random element
                $element = $this->getRandomElement($elements);

                // Add to results with the singular type name
                $cards[$singularType] = $element;
            }
        }

        return [
            'tableTitle' => 'Dice Rolls',
            'cards' => $cards
        ];
    }

    /**
     * Get a random element from an array
     *
     * @param array $elements Array of elements
     * @return string|null Random element or null if array is empty
     */
    private function getRandomElement(array $elements): ?string
    {
        if (empty($elements)) {
            return null;
        }

        return $elements[array_rand($elements)];
    }

    /**
     * Get multiple random elements from an array
     *
     * @param array $elements Array of elements
     * @param int $count Number of elements to get
     * @return array Array of random elements
     */
    private function getRandomElements(array $elements, int $count): array
    {
        if (empty($elements)) {
            return [];
        }

        // If count is greater than the number of elements, return all elements in random order
        if ($count >= count($elements)) {
            $result = $elements;
            shuffle($result);
            return $result;
        }

        // Otherwise, get $count random elements
        $result = [];
        $keys = array_rand($elements, $count);

        // If only one element is requested, array_rand returns a single key, not an array
        if ($count === 1) {
            $result[] = $elements[$keys];
        } else {
            foreach ($keys as $key) {
                $result[] = $elements[$key];
            }
        }

        return $result;
    }

    /**
     * Convert singular type name to plural
     *
     * @param string $type Singular type name
     * @return string Plural type name
     */
    private function singularToPlural(string $type): string
    {
        // If irregular plurals are needed, added them here
        $map = [
            'company' => 'companies',
            'business' => 'businesses',
            'person' => 'people',
            'child' => 'children',
        ];

        return $map[$type] ?? $type . 's';
    }

    /**
     * Generate a random plot from the outgunned plots collection
     *
     * @param string $genre The genre to filter by (any, all, action, adventure, spy_thriller, blockbuster)
     * @return array The generated plot components
     * @throws \Exception If plot generation fails
     */
    public function generateOutgunnedPlot(string $genre): array
    {
        $plots = $this->seedLoader->loadOutgunnedPlots();

        // If genre is 'any', randomly select from available genres
        if ($genre === 'any') {
            $availableGenres = array_keys($plots);
            if (empty($availableGenres)) {
                throw new \RuntimeException('No genres available in outgunned plots data');
            }
            $randomIndex = array_rand($availableGenres);
            $genre = $availableGenres[$randomIndex];
        }

        // If genre is 'all', merge content from all genres
        if ($genre === 'all') {
            $mergedContent = [
                'characters' => ['adjectives' => [], 'nouns' => []],
                'settings' => ['adjectives' => [], 'nouns' => []],
                'events' => ['verbs' => [], 'objects' => [], 'modifiers' => []],
                'objects' => ['qualities' => [], 'items' => []]
            ];

            // Merge all content from each genre
            foreach ($plots as $genreData) {
                foreach (['characters', 'settings'] as $category) {
                    $mergedContent[$category]['adjectives'] = array_merge(
                        $mergedContent[$category]['adjectives'],
                        $genreData[$category]['adjectives'] ?? []
                    );
                    $mergedContent[$category]['nouns'] = array_merge(
                        $mergedContent[$category]['nouns'],
                        $genreData[$category]['nouns'] ?? []
                    );
                }

                foreach (['verbs', 'objects', 'modifiers'] as $type) {
                    $mergedContent['events'][$type] = array_merge(
                        $mergedContent['events'][$type],
                        $genreData['events'][$type] ?? []
                    );
                }

                $mergedContent['objects']['qualities'] = array_merge(
                    $mergedContent['objects']['qualities'],
                    $genreData['objects']['qualities'] ?? []
                );
                $mergedContent['objects']['items'] = array_merge(
                    $mergedContent['objects']['items'],
                    $genreData['objects']['items'] ?? []
                );
            }

            // Remove duplicates from merged content
            foreach (['characters', 'settings'] as $category) {
                $mergedContent[$category]['adjectives'] = array_unique($mergedContent[$category]['adjectives']);
                $mergedContent[$category]['nouns'] = array_unique($mergedContent[$category]['nouns']);
            }
            foreach (['verbs', 'objects', 'modifiers'] as $type) {
                $mergedContent['events'][$type] = array_unique($mergedContent['events'][$type]);
            }
            $mergedContent['objects']['qualities'] = array_unique($mergedContent['objects']['qualities']);
            $mergedContent['objects']['items'] = array_unique($mergedContent['objects']['items']);

            $genreData = $mergedContent;
        } else {
            if (!isset($plots[$genre])) {
                throw new \InvalidArgumentException("Invalid genre: $genre");
            }
            $genreData = $plots[$genre];
        }

        // Get random elements from each category
        $character = $this->combineRandomElements(
            $genreData['characters']['adjectives'],
            $genreData['characters']['nouns']
        );
        $setting = $this->combineRandomElements(
            $genreData['settings']['adjectives'],
            $genreData['settings']['nouns']
        );
        $event = $this->generateEventPhrase($genreData['events']);
        $object = $this->combineRandomElements(
            $genreData['objects']['qualities'],
            $genreData['objects']['items']
        );

        return [
            'character' => $character,
            'setting' => $setting,
            'event' => $event,
            'object' => $object
        ];
    }

    /**
     * Helper method to combine random elements from two arrays
     *
     * @param array $first First array of elements
     * @param array $second Second array of elements
     * @return string Combined random elements
     */
    private function combineRandomElements(array $first, array $second): string
    {
        $firstElement = $first[array_rand($first)];
        $secondElement = $second[array_rand($second)];
        return "$firstElement $secondElement";
    }

    /**
     * Helper method to generate an event phrase
     *
     * @param array $eventData Event data containing verbs, objects, and modifiers
     * @return string Generated event phrase
     */
    private function generateEventPhrase(array $eventData): string
    {
        $verb = $eventData['verbs'][array_rand($eventData['verbs'])];
        $object = $eventData['objects'][array_rand($eventData['objects'])];
        $modifier = $eventData['modifiers'][array_rand($eventData['modifiers'])];
        return "$verb $object $modifier";
    }
}
