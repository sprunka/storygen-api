<?php

namespace App\Scripts;

/**
 * Script to parse existing seed entries into component parts
 * 
 * This script analyzes the existing seed data and extracts modular components
 * (adjectives, nouns, verbs, objects, modifiers, qualities, items) to create
 * a new modular seed data structure.
 */
class SeedParser
{
    /**
     * @var string Path to the original seed data file
     */
    private string $originalSeedPath;

    /**
     * @var string Path to the new modular seed data file
     */
    private string $modularSeedPath;

    /**
     * @var array Original seed data
     */
    private array $originalSeedData;

    /**
     * @var array Modular seed data
     */
    private array $modularSeedData;

    /**
     * Constructor
     *
     * @param string|null $originalSeedPath Path to the original seed data file
     * @param string|null $modularSeedPath Path to the new modular seed data file
     */
    public function __construct(
        string $originalSeedPath = null,
        string $modularSeedPath = null
    ) {
        $this->originalSeedPath = $originalSeedPath ?? __DIR__ . '/../../data/seed.json';
        $this->modularSeedPath = $modularSeedPath ?? __DIR__ . '/../../data/modular_seeds.json';
        $this->modularSeedData = [
            'kids' => $this->createEmptyModularStructure(),
            'teens' => $this->createEmptyModularStructure(),
            'adults' => $this->createEmptyModularStructure()
        ];
    }

    /**
     * Create an empty modular structure
     *
     * @return array Empty modular structure
     */
    private function createEmptyModularStructure(): array
    {
        return [
            'characters' => [
                'adjectives' => [],
                'nouns' => []
            ],
            'settings' => [
                'adjectives' => [],
                'nouns' => []
            ],
            'events' => [
                'verbs' => [],
                'objects' => [],
                'modifiers' => []
            ],
            'objects' => [
                'qualities' => [],
                'items' => []
            ]
        ];
    }

    /**
     * Load original seed data
     *
     * @return void
     * @throws \RuntimeException If seed file cannot be read or parsed
     */
    public function loadOriginalSeedData(): void
    {
        if (!file_exists($this->originalSeedPath)) {
            throw new \RuntimeException("Seed file not found: {$this->originalSeedPath}");
        }

        $jsonData = file_get_contents($this->originalSeedPath);
        if ($jsonData === false) {
            throw new \RuntimeException("Failed to read seed file: {$this->originalSeedPath}");
        }

        $data = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Failed to parse seed file: " . json_last_error_msg());
        }

        $this->originalSeedData = $data;
    }

    /**
     * Parse original seed data into modular components
     *
     * @return void
     */
    public function parseOriginalSeedData(): void
    {
        foreach ($this->originalSeedData as $ageGroup => $categories) {
            foreach ($categories as $category => $entries) {
                foreach ($entries as $entry) {
                    $this->parseEntry($ageGroup, $category, $entry);
                }
            }
        }
    }

    /**
     * Parse a single entry into modular components
     *
     * @param string $ageGroup Age group (kids, teens, adults)
     * @param string $category Category (characters, settings, events, objects)
     * @param string $entry Entry to parse
     * @return void
     */
    private function parseEntry(string $ageGroup, string $category, string $entry): void
    {
        switch ($category) {
            case 'characters':
                $this->parseCharacter($ageGroup, $entry);
                break;
            case 'settings':
                $this->parseSetting($ageGroup, $entry);
                break;
            case 'events':
                $this->parseEvent($ageGroup, $entry);
                break;
            case 'objects':
                $this->parseObject($ageGroup, $entry);
                break;
        }
    }

    /**
     * Parse a character entry into adjective and noun
     *
     * @param string $ageGroup Age group (kids, teens, adults)
     * @param string $entry Character entry to parse
     * @return void
     */
    private function parseCharacter(string $ageGroup, string $entry): void
    {
        // Most characters follow the pattern: [adjective] [noun]
        // Some have more complex structures, but we'll use a simple approach for now
        $parts = explode(' ', $entry, 2);
        if (count($parts) === 2) {
            $adjective = $parts[0];
            $noun = $parts[1];
            
            // Add to modular seed data if not already present
            if (!in_array($adjective, $this->modularSeedData[$ageGroup]['characters']['adjectives'])) {
                $this->modularSeedData[$ageGroup]['characters']['adjectives'][] = $adjective;
            }
            if (!in_array($noun, $this->modularSeedData[$ageGroup]['characters']['nouns'])) {
                $this->modularSeedData[$ageGroup]['characters']['nouns'][] = $noun;
            }
        }
    }

    /**
     * Parse a setting entry into adjective and noun
     *
     * @param string $ageGroup Age group (kids, teens, adults)
     * @param string $entry Setting entry to parse
     * @return void
     */
    private function parseSetting(string $ageGroup, string $entry): void
    {
        // Most settings follow the pattern: [adjective] [noun]
        // Some have more complex structures, but we'll use a simple approach for now
        $parts = explode(' ', $entry, 2);
        if (count($parts) === 2) {
            $adjective = $parts[0];
            $noun = $parts[1];
            
            // Add to modular seed data if not already present
            if (!in_array($adjective, $this->modularSeedData[$ageGroup]['settings']['adjectives'])) {
                $this->modularSeedData[$ageGroup]['settings']['adjectives'][] = $adjective;
            }
            if (!in_array($noun, $this->modularSeedData[$ageGroup]['settings']['nouns'])) {
                $this->modularSeedData[$ageGroup]['settings']['nouns'][] = $noun;
            }
        }
    }

    /**
     * Parse an event entry into verb, object, and optional modifier
     *
     * @param string $ageGroup Age group (kids, teens, adults)
     * @param string $entry Event entry to parse
     * @return void
     */
    private function parseEvent(string $ageGroup, string $entry): void
    {
        // Events typically follow the pattern: [verb] [object/trigger] or [verb] [object/trigger] [modifier]
        // This is more complex, so we'll use a simple approach for now
        $parts = explode(' ', $entry, 2);
        if (count($parts) === 2) {
            $verb = $parts[0];
            $object = $parts[1];
            $modifier = null;
            
            // Check for common modifiers
            $modifiers = ['accidentally', 'suddenly', 'magically', 'completely', 'quietly',
                          'bravely', 'secretly', 'happily', 'carefully', 'quickly',
                          'slowly', 'loudly', 'surprisingly', 'mysteriously', 'excitedly'];
            
            foreach ($modifiers as $mod) {
                if (strpos($object, $mod) !== false) {
                    $modifier = $mod;
                    $object = str_replace($mod . ' ', '', $object);
                    break;
                }
            }
            
            // Add to modular seed data if not already present
            if (!in_array($verb, $this->modularSeedData[$ageGroup]['events']['verbs'])) {
                $this->modularSeedData[$ageGroup]['events']['verbs'][] = $verb;
            }
            if (!in_array($object, $this->modularSeedData[$ageGroup]['events']['objects'])) {
                $this->modularSeedData[$ageGroup]['events']['objects'][] = $object;
            }
            if ($modifier !== null && !in_array($modifier, $this->modularSeedData[$ageGroup]['events']['modifiers'])) {
                $this->modularSeedData[$ageGroup]['events']['modifiers'][] = $modifier;
            }
        }
    }

    /**
     * Parse an object entry into quality and item
     *
     * @param string $ageGroup Age group (kids, teens, adults)
     * @param string $entry Object entry to parse
     * @return void
     */
    private function parseObject(string $ageGroup, string $entry): void
    {
        // Objects typically follow the pattern: [quality/adjective] [item]
        // Some have more complex structures, but we'll use a simple approach for now
        $parts = explode(' ', $entry, 2);
        if (count($parts) === 2) {
            $quality = $parts[0];
            $item = $parts[1];
            
            // Add to modular seed data if not already present
            if (!in_array($quality, $this->modularSeedData[$ageGroup]['objects']['qualities'])) {
                $this->modularSeedData[$ageGroup]['objects']['qualities'][] = $quality;
            }
            if (!in_array($item, $this->modularSeedData[$ageGroup]['objects']['items'])) {
                $this->modularSeedData[$ageGroup]['objects']['items'][] = $item;
            }
        }
    }

    /**
     * Save modular seed data to file
     *
     * @return void
     * @throws \RuntimeException If seed file cannot be written
     */
    public function saveModularSeedData(): void
    {
        $jsonData = json_encode($this->modularSeedData, JSON_PRETTY_PRINT);
        if ($jsonData === false) {
            throw new \RuntimeException("Failed to encode modular seed data: " . json_last_error_msg());
        }

        $result = file_put_contents($this->modularSeedPath, $jsonData);
        if ($result === false) {
            throw new \RuntimeException("Failed to write modular seed file: {$this->modularSeedPath}");
        }
    }

    /**
     * Run the parser
     *
     * @return void
     */
    public function run(): void
    {
        $this->loadOriginalSeedData();
        $this->parseOriginalSeedData();
        $this->saveModularSeedData();
        echo "Seed data parsed successfully.\n";
    }
}

// Run the parser if this script is executed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $parser = new SeedParser();
    $parser->run();
}