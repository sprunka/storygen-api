<?php

namespace StoryGen\Services;

/**
 * @OA\Schema(
 *     schema="PromptElements",
 *     description="Story prompt elements",
 *     @OA\Property(property="characters", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="settings", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="events", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="objects", type="array", @OA\Items(type="string"))
 * )
 */
class SeedLoader
{
    /**
     * @var string Path to the original seed data file
     */
    private string $seedPath;

    /**
     * @var string Path to the modular seed data file
     */
    private string $modularSeedPath;

    /**
     * @var array|null Cached original seed data
     */
    private ?array $seedData = null;

    /**
     * @var array|null Cached modular seed data
     */
    private ?array $modularSeedData = null;

    /**
     * @var bool Whether to use modular seed data
     */
    private bool $useModularData;

    /**
     * Constructor
     *
     * @param string|null $seedPath Path to the original seed data file
     * @param string|null $modularSeedPath Path to the modular seed data file
     * @param bool $useModularData Whether to use modular seed data
     */
    public function __construct(
        string $seedPath = null,
        string $modularSeedPath = null,
        bool $useModularData = true
    ) {
        $this->seedPath = $seedPath ?? __DIR__ . '/../../../data/seed.json';
        $this->modularSeedPath = $modularSeedPath ?? __DIR__ . '/../../../data/modular_seeds.json';
        $this->useModularData = $useModularData;
    }

    /**
     * Load original seed data from JSON file
     *
     * @return array Original seed data
     * @throws \RuntimeException If seed file cannot be read or parsed
     */
    public function loadSeedData(): array
    {
        if ($this->seedData !== null) {
            return $this->seedData;
        }

        if (!file_exists($this->seedPath)) {
            throw new \RuntimeException("Seed file not found: {$this->seedPath}");
        }

        $jsonData = file_get_contents($this->seedPath);
        if ($jsonData === false) {
            throw new \RuntimeException("Failed to read seed file: {$this->seedPath}");
        }

        $data = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Failed to parse seed file: " . json_last_error_msg());
        }

        $this->seedData = $data;
        return $data;
    }

    /**
     * Load modular seed data from JSON file
     *
     * @return array Modular seed data
     * @throws \RuntimeException If modular seed file cannot be read or parsed
     */
    public function loadModularSeedData(): array
    {
        if ($this->modularSeedData !== null) {
            return $this->modularSeedData;
        }

        // If modular seed file doesn't exist, fall back to original seed data
        if (!file_exists($this->modularSeedPath)) {
            $this->useModularData = false;
            return $this->loadSeedData();
        }

        $jsonData = file_get_contents($this->modularSeedPath);
        if ($jsonData === false) {
            throw new \RuntimeException("Failed to read modular seed file: {$this->modularSeedPath}");
        }

        $data = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Failed to parse modular seed file: " . json_last_error_msg());
        }

        $this->modularSeedData = $data;
        return $data;
    }

    /**
     * Get prompt elements for a specific age group
     *
     * @param string|null $ageGroup Age group (kids, teens, adults)
     * @return array Prompt elements for the specified age group
     * @throws \InvalidArgumentException If age group is invalid
     */
    public function getPromptElementsByAgeGroup(?string $ageGroup = null): array
    {
        // If using modular data, convert it to the original format
        if ($this->useModularData) {
            return $this->getPromptElementsFromModularData($ageGroup);
        }

        $data = $this->loadSeedData();

        // If no age group specified, merge all age groups
        if ($ageGroup === null || $ageGroup === 'any') {
            $mergedElements = [
                'characters' => [],
                'settings' => [],
                'events' => [],
                'objects' => []
            ];

            // Merge elements from all age groups
            foreach ($data as $group => $elements) {
                foreach (['characters', 'settings', 'events', 'objects'] as $type) {
                    if (isset($elements[$type]) && is_array($elements[$type])) {
                        $mergedElements[$type] = array_merge($mergedElements[$type], $elements[$type]);
                    }
                }
            }

            return $mergedElements;
        }

        // Validate age group
        if (!isset($data[$ageGroup])) {
            throw new \InvalidArgumentException("Invalid age group: {$ageGroup}");
        }

        return $data[$ageGroup];
    }

    /**
     * Get prompt elements from modular data for a specific age group
     *
     * @param string|null $ageGroup Age group (kids, teens, adults)
     * @return array Prompt elements for the specified age group
     * @throws \InvalidArgumentException If age group is invalid
     */
    private function getPromptElementsFromModularData(?string $ageGroup = null): array
    {
        $data = $this->loadModularSeedData();

        // If no age group specified or 'any', merge all age groups
        if ($ageGroup === null || $ageGroup === 'any') {
            $mergedElements = [
                'characters' => [],
                'settings' => [],
                'events' => [],
                'objects' => []
            ];

            // Generate combined elements from all age groups
            foreach ($data as $group => $categories) {
                // Generate characters
                if (isset($categories['characters'])) {
                    $characters = $this->generateCombinedElements(
                        $categories['characters']['adjectives'] ?? [],
                        $categories['characters']['nouns'] ?? []
                    );
                    $mergedElements['characters'] = array_merge($mergedElements['characters'], $characters);
                }

                // Generate settings
                if (isset($categories['settings'])) {
                    $settings = $this->generateCombinedElements(
                        $categories['settings']['adjectives'] ?? [],
                        $categories['settings']['nouns'] ?? []
                    );
                    $mergedElements['settings'] = array_merge($mergedElements['settings'], $settings);
                }

                // Generate events
                if (isset($categories['events'])) {
                    $events = $this->generateCombinedEvents(
                        $categories['events']['verbs'] ?? [],
                        $categories['events']['objects'] ?? [],
                        $categories['events']['modifiers'] ?? []
                    );
                    $mergedElements['events'] = array_merge($mergedElements['events'], $events);
                }

                // Generate objects
                if (isset($categories['objects'])) {
                    $objects = $this->generateCombinedElements(
                        $categories['objects']['qualities'] ?? [],
                        $categories['objects']['items'] ?? []
                    );
                    $mergedElements['objects'] = array_merge($mergedElements['objects'], $objects);
                }
            }

            return $mergedElements;
        }

        // Validate age group
        if (!isset($data[$ageGroup])) {
            throw new \InvalidArgumentException("Invalid age group: {$ageGroup}");
        }

        $categories = $data[$ageGroup];
        $elements = [
            'characters' => [],
            'settings' => [],
            'events' => [],
            'objects' => []
        ];

        // Generate characters
        if (isset($categories['characters'])) {
            $elements['characters'] = $this->generateCombinedElements(
                $categories['characters']['adjectives'] ?? [],
                $categories['characters']['nouns'] ?? []
            );
        }

        // Generate settings
        if (isset($categories['settings'])) {
            $elements['settings'] = $this->generateCombinedElements(
                $categories['settings']['adjectives'] ?? [],
                $categories['settings']['nouns'] ?? []
            );
        }

        // Generate events
        if (isset($categories['events'])) {
            $elements['events'] = $this->generateCombinedEvents(
                $categories['events']['verbs'] ?? [],
                $categories['events']['objects'] ?? [],
                $categories['events']['modifiers'] ?? []
            );
        }

        // Generate objects
        if (isset($categories['objects'])) {
            $elements['objects'] = $this->generateCombinedElements(
                $categories['objects']['qualities'] ?? [],
                $categories['objects']['items'] ?? []
            );
        }

        return $elements;
    }

    /**
     * Generate combined elements from two arrays
     *
     * @param array $first First array of components
     * @param array $second Second array of components
     * @return array Combined elements
     */
    private function generateCombinedElements(array $first, array $second): array
    {
        $combined = [];

        // If either array is empty, return an empty array
        if (empty($first) || empty($second)) {
            return $combined;
        }

        // Generate all possible combinations
        foreach ($first as $firstComponent) {
            foreach ($second as $secondComponent) {
                $combined[] = $firstComponent . ' ' . $secondComponent;
            }
        }

        return $combined;
    }

    /**
     * Generate combined events from verbs, objects, and modifiers
     *
     * @param array $verbs Verbs
     * @param array $objects Objects
     * @param array $modifiers Modifiers (optional)
     * @return array Combined events
     */
    private function generateCombinedEvents(array $verbs, array $objects, array $modifiers = []): array
    {
        $combined = [];

        // If either verbs or objects is empty, return an empty array
        if (empty($verbs) || empty($objects)) {
            return $combined;
        }

        // Generate combinations without modifiers
        foreach ($verbs as $verb) {
            foreach ($objects as $object) {
                $combined[] = $verb . ' ' . $object;
            }
        }

        // If modifiers are provided, generate combinations with modifiers
        if (!empty($modifiers)) {
            $withoutModifiers = $combined;
            $combined = [];

            // Add some events without modifiers
            $combined = array_merge($combined, array_slice($withoutModifiers, 0, count($withoutModifiers) * 2 / 3));

            // Add some events with modifiers
            foreach ($verbs as $verb) {
                foreach ($objects as $object) {
                    foreach ($modifiers as $modifier) {
                        $combined[] = $verb . ' ' . $modifier . ' ' . $object;
                    }
                }
            }
        }

        return $combined;
    }

    /**
     * Get all available elements of a specific type across all age groups
     *
     * @param string $type Element type (characters, settings, events, objects)
     * @return array All elements of the specified type
     * @throws \InvalidArgumentException If element type is invalid
     */
    public function getAllElementsByType(string $type): array
    {
        // If using modular data, get elements from modular data
        if ($this->useModularData) {
            return $this->getAllElementsByTypeFromModularData($type);
        }

        $data = $this->loadSeedData();
        $elements = [];

        // Validate element type
        $validTypes = ['characters', 'settings', 'events', 'objects'];
        if (!in_array($type, $validTypes)) {
            throw new \InvalidArgumentException("Invalid element type: {$type}");
        }

        // Collect elements of the specified type from all age groups
        foreach ($data as $ageGroup) {
            if (isset($ageGroup[$type]) && is_array($ageGroup[$type])) {
                foreach ($ageGroup[$type] as $element) {
                    $elements[] = $element;
                }
            }
        }

        return $elements;
    }

    /**
     * Get all available elements of a specific type across all age groups from modular data
     *
     * @param string $type Element type (characters, settings, events, objects)
     * @return array All elements of the specified type
     * @throws \InvalidArgumentException If element type is invalid
     */
    private function getAllElementsByTypeFromModularData(string $type): array
    {
        $data = $this->loadModularSeedData();
        $elements = [];

        // Validate element type
        $validTypes = ['characters', 'settings', 'events', 'objects'];
        if (!in_array($type, $validTypes)) {
            throw new \InvalidArgumentException("Invalid element type: {$type}");
        }

        // Generate combined elements for the specified type from all age groups
        foreach ($data as $ageGroup => $categories) {
            if (isset($categories[$type])) {
                switch ($type) {
                    case 'characters':
                        $combined = $this->generateCombinedElements(
                            $categories[$type]['adjectives'] ?? [],
                            $categories[$type]['nouns'] ?? []
                        );
                        $elements = array_merge($elements, $combined);
                        break;
                    case 'settings':
                        $combined = $this->generateCombinedElements(
                            $categories[$type]['adjectives'] ?? [],
                            $categories[$type]['nouns'] ?? []
                        );
                        $elements = array_merge($elements, $combined);
                        break;
                    case 'events':
                        $combined = $this->generateCombinedEvents(
                            $categories[$type]['verbs'] ?? [],
                            $categories[$type]['objects'] ?? [],
                            $categories[$type]['modifiers'] ?? []
                        );
                        $elements = array_merge($elements, $combined);
                        break;
                    case 'objects':
                        $combined = $this->generateCombinedElements(
                            $categories[$type]['qualities'] ?? [],
                            $categories[$type]['items'] ?? []
                        );
                        $elements = array_merge($elements, $combined);
                        break;
                }
            }
        }

        return $elements;
    }

    /**
     * Get modular components for a specific age group and category
     *
     * @param string|null $ageGroup Age group (kids, teens, adults)
     * @param string $category Category (characters, settings, events, objects)
     * @param string $componentType Component type (adjectives, nouns, verbs, objects, modifiers, qualities, items)
     * @return array Modular components
     * @throws \InvalidArgumentException If age group, category, or component type is invalid
     */
    public function getModularComponents(?string $ageGroup, string $category, string $componentType): array
    {
        $data = $this->loadModularSeedData();

        // Validate category
        $validCategories = ['characters', 'settings', 'events', 'objects'];
        if (!in_array($category, $validCategories)) {
            throw new \InvalidArgumentException("Invalid category: {$category}");
        }

        // Validate component type based on category
        $validComponentTypes = [
            'characters' => ['adjectives', 'nouns'],
            'settings' => ['adjectives', 'nouns'],
            'events' => ['verbs', 'objects', 'modifiers'],
            'objects' => ['qualities', 'items']
        ];
        if (!in_array($componentType, $validComponentTypes[$category])) {
            throw new \InvalidArgumentException("Invalid component type for category {$category}: {$componentType}");
        }

        // If no age group specified or 'any', merge components from all age groups
        if ($ageGroup === null || $ageGroup === 'any') {
            $allComponents = [];

            // Collect all components first
            foreach ($data as $group => $categories) {
                if (isset($categories[$category][$componentType])) {
                    $allComponents[] = $categories[$category][$componentType];
                }
            }

            // Merge all components at once
            $mergedComponents = !empty($allComponents) ? array_merge(...$allComponents) : [];

            return array_values(array_unique($mergedComponents));
        }

        // Validate age group
        if (!isset($data[$ageGroup])) {
            throw new \InvalidArgumentException("Invalid age group: {$ageGroup}");
        }

        // Return components for the specified age group, category, and component type
        return $data[$ageGroup][$category][$componentType] ?? [];
    }
}
