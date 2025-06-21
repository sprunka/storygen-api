<?php

namespace Tests;

use App\Services\SeedLoader;
use PHPUnit\Framework\TestCase;

class SeedLoaderBackwardCompatibilityTest extends TestCase
{
    /**
     * @var string Path to the test original seed data file
     */
    private string $originalSeedPath;

    /**
     * @var string Path to the test modular seed data file
     */
    private string $modularSeedPath;

    /**
     * Set up the test environment
     */
    protected function setUp(): void
    {
        // Create temporary test files
        $this->originalSeedPath = sys_get_temp_dir() . '/test_seed.json';
        $this->modularSeedPath = sys_get_temp_dir() . '/test_modular_seeds.json';

        // Create test original seed data
        $originalSeedData = [
            'kids' => [
                'characters' => ['curious child', 'friendly robot'],
                'settings' => ['enchanted forest', 'space station'],
                'events' => ['finds a treasure map', 'discovers a secret door'],
                'objects' => ['magic wand', 'talking toy']
            ],
            'teens' => [
                'characters' => ['rebellious teenager', 'young inventor'],
                'settings' => ['abandoned mall', 'summer camp'],
                'events' => ['uncovers a conspiracy', 'competes in a contest'],
                'objects' => ['mysterious journal', 'vintage camera']
            ]
        ];

        // Create test modular seed data
        $modularSeedData = [
            'kids' => [
                'characters' => [
                    'adjectives' => ['curious', 'friendly'],
                    'nouns' => ['child', 'robot']
                ],
                'settings' => [
                    'adjectives' => ['enchanted', 'space'],
                    'nouns' => ['forest', 'station']
                ],
                'events' => [
                    'verbs' => ['finds', 'discovers'],
                    'objects' => ['a treasure map', 'a secret door'],
                    'modifiers' => ['accidentally', 'suddenly']
                ],
                'objects' => [
                    'qualities' => ['magic', 'talking'],
                    'items' => ['wand', 'toy']
                ]
            ],
            'teens' => [
                'characters' => [
                    'adjectives' => ['rebellious', 'young'],
                    'nouns' => ['teenager', 'inventor']
                ],
                'settings' => [
                    'adjectives' => ['abandoned', 'summer'],
                    'nouns' => ['mall', 'camp']
                ],
                'events' => [
                    'verbs' => ['uncovers', 'competes'],
                    'objects' => ['a conspiracy', 'in a contest'],
                    'modifiers' => ['secretly', 'bravely']
                ],
                'objects' => [
                    'qualities' => ['mysterious', 'vintage'],
                    'items' => ['journal', 'camera']
                ]
            ]
        ];

        // Write test data to files
        file_put_contents($this->originalSeedPath, json_encode($originalSeedData));
        file_put_contents($this->modularSeedPath, json_encode($modularSeedData));
    }

    /**
     * Clean up after tests
     */
    protected function tearDown(): void
    {
        // Remove temporary test files
        if (file_exists($this->originalSeedPath)) {
            unlink($this->originalSeedPath);
        }
        if (file_exists($this->modularSeedPath)) {
            unlink($this->modularSeedPath);
        }
    }

    /**
     * Test loading original seed data
     */
    public function testLoadOriginalSeedData(): void
    {
        // Create a seed loader with original seed data only
        $seedLoader = new SeedLoader($this->originalSeedPath, 'nonexistent_file.json', false);

        // Load the seed data
        $data = $seedLoader->loadSeedData();

        // Check that the data has the expected structure
        $this->assertIsArray($data);
        $this->assertArrayHasKey('kids', $data);
        $this->assertArrayHasKey('teens', $data);

        // Check that the data contains the expected values
        $this->assertEquals(['curious child', 'friendly robot'], $data['kids']['characters']);
        $this->assertEquals(['enchanted forest', 'space station'], $data['kids']['settings']);
        $this->assertEquals(['finds a treasure map', 'discovers a secret door'], $data['kids']['events']);
        $this->assertEquals(['magic wand', 'talking toy'], $data['kids']['objects']);
    }

    /**
     * Test loading modular seed data
     */
    public function testLoadModularSeedData(): void
    {
        // Create a seed loader with modular seed data
        $seedLoader = new SeedLoader($this->originalSeedPath, $this->modularSeedPath, true);

        // Load the modular seed data
        $data = $seedLoader->loadModularSeedData();

        // Check that the data has the expected structure
        $this->assertIsArray($data);
        $this->assertArrayHasKey('kids', $data);
        $this->assertArrayHasKey('teens', $data);

        // Check that the data contains the expected values
        $this->assertEquals(['curious', 'friendly'], $data['kids']['characters']['adjectives']);
        $this->assertEquals(['child', 'robot'], $data['kids']['characters']['nouns']);
        $this->assertEquals(['enchanted', 'space'], $data['kids']['settings']['adjectives']);
        $this->assertEquals(['forest', 'station'], $data['kids']['settings']['nouns']);
        $this->assertEquals(['finds', 'discovers'], $data['kids']['events']['verbs']);
        $this->assertEquals(['a treasure map', 'a secret door'], $data['kids']['events']['objects']);
        $this->assertEquals(['accidentally', 'suddenly'], $data['kids']['events']['modifiers']);
        $this->assertEquals(['magic', 'talking'], $data['kids']['objects']['qualities']);
        $this->assertEquals(['wand', 'toy'], $data['kids']['objects']['items']);
    }

    /**
     * Test fallback to original seed data when modular seed data is not available
     */
    public function testFallbackToOriginalSeedData(): void
    {
        // Create a seed loader with a nonexistent modular seed file
        $seedLoader = new SeedLoader($this->originalSeedPath, 'nonexistent_file.json', true);

        // Load the modular seed data (should fall back to original)
        $data = $seedLoader->loadModularSeedData();

        // Check that the data has the expected structure (original format)
        $this->assertIsArray($data);
        $this->assertArrayHasKey('kids', $data);
        $this->assertArrayHasKey('teens', $data);

        // Check that the data contains the expected values (original format)
        $this->assertEquals(['curious child', 'friendly robot'], $data['kids']['characters']);
        $this->assertEquals(['enchanted forest', 'space station'], $data['kids']['settings']);
        $this->assertEquals(['finds a treasure map', 'discovers a secret door'], $data['kids']['events']);
        $this->assertEquals(['magic wand', 'talking toy'], $data['kids']['objects']);
    }

    /**
     * Test getting prompt elements by age group with original seed data
     */
    public function testGetPromptElementsByAgeGroupWithOriginalSeedData(): void
    {
        // Create a seed loader with original seed data only
        $seedLoader = new SeedLoader($this->originalSeedPath, 'nonexistent_file.json', false);

        // Get prompt elements for kids
        $elements = $seedLoader->getPromptElementsByAgeGroup('kids');

        // Check that the elements have the expected structure
        $this->assertIsArray($elements);
        $this->assertArrayHasKey('characters', $elements);
        $this->assertArrayHasKey('settings', $elements);
        $this->assertArrayHasKey('events', $elements);
        $this->assertArrayHasKey('objects', $elements);

        // Check that the elements contain the expected values
        $this->assertEquals(['curious child', 'friendly robot'], $elements['characters']);
        $this->assertEquals(['enchanted forest', 'space station'], $elements['settings']);
        $this->assertEquals(['finds a treasure map', 'discovers a secret door'], $elements['events']);
        $this->assertEquals(['magic wand', 'talking toy'], $elements['objects']);
    }

    /**
     * Test getting prompt elements by age group with modular seed data
     */
    public function testGetPromptElementsByAgeGroupWithModularSeedData(): void
    {
        // Create a seed loader with modular seed data
        $seedLoader = new SeedLoader($this->originalSeedPath, $this->modularSeedPath, true);

        // Get prompt elements for kids
        $elements = $seedLoader->getPromptElementsByAgeGroup('kids');

        // Check that the elements have the expected structure
        $this->assertIsArray($elements);
        $this->assertArrayHasKey('characters', $elements);
        $this->assertArrayHasKey('settings', $elements);
        $this->assertArrayHasKey('events', $elements);
        $this->assertArrayHasKey('objects', $elements);

        // Check that the elements contain the expected values (combined from modular components)
        $this->assertContains('curious child', $elements['characters']);
        $this->assertContains('friendly robot', $elements['characters']);
        $this->assertContains('curious robot', $elements['characters']);
        $this->assertContains('friendly child', $elements['characters']);

        $this->assertContains('enchanted forest', $elements['settings']);
        $this->assertContains('space station', $elements['settings']);
        $this->assertContains('enchanted station', $elements['settings']);
        $this->assertContains('space forest', $elements['settings']);

        // Events might have modifiers, so we need to check for various combinations
        $this->assertCount(count($elements['events']), array_filter($elements['events'], function ($event) {
            return strpos($event, 'finds') === 0 || strpos($event, 'discovers') === 0;
        }));

        $this->assertContains('magic wand', $elements['objects']);
        $this->assertContains('talking toy', $elements['objects']);
        $this->assertContains('magic toy', $elements['objects']);
        $this->assertContains('talking wand', $elements['objects']);
    }

    /**
     * Test getting all elements by type with original seed data
     */
    public function testGetAllElementsByTypeWithOriginalSeedData(): void
    {
        // Create a seed loader with original seed data only
        $seedLoader = new SeedLoader($this->originalSeedPath, 'nonexistent_file.json', false);

        // Get all characters
        $characters = $seedLoader->getAllElementsByType('characters');

        // Check that the characters have the expected values
        $this->assertIsArray($characters);
        $this->assertContains('curious child', $characters);
        $this->assertContains('friendly robot', $characters);
        $this->assertContains('rebellious teenager', $characters);
        $this->assertContains('young inventor', $characters);
    }

    /**
     * Test getting all elements by type with modular seed data
     */
    public function testGetAllElementsByTypeWithModularSeedData(): void
    {
        // Create a seed loader with modular seed data
        $seedLoader = new SeedLoader($this->originalSeedPath, $this->modularSeedPath, true);

        // Get all characters
        $characters = $seedLoader->getAllElementsByType('characters');

        // Check that the characters have the expected values (combined from modular components)
        $this->assertIsArray($characters);
        $this->assertContains('curious child', $characters);
        $this->assertContains('friendly robot', $characters);
        $this->assertContains('curious robot', $characters);
        $this->assertContains('friendly child', $characters);
        $this->assertContains('rebellious teenager', $characters);
        $this->assertContains('young inventor', $characters);
        $this->assertContains('rebellious inventor', $characters);
        $this->assertContains('young teenager', $characters);
    }

    /**
     * Test getting modular components
     */
    public function testGetModularComponents(): void
    {
        // Create a seed loader with modular seed data
        $seedLoader = new SeedLoader($this->originalSeedPath, $this->modularSeedPath, true);

        // Get character adjectives for kids
        $adjectives = $seedLoader->getModularComponents('kids', 'characters', 'adjectives');

        // Check that the adjectives have the expected values
        $this->assertIsArray($adjectives);
        $this->assertEquals(['curious', 'friendly'], $adjectives);

        // Get character nouns for kids
        $nouns = $seedLoader->getModularComponents('kids', 'characters', 'nouns');

        // Check that the nouns have the expected values
        $this->assertIsArray($nouns);
        $this->assertEquals(['child', 'robot'], $nouns);

        // Get character adjectives for all age groups
        $allAdjectives = $seedLoader->getModularComponents(null, 'characters', 'adjectives');

        // Check that the adjectives have the expected values
        $this->assertIsArray($allAdjectives);
        $this->assertContains('curious', $allAdjectives);
        $this->assertContains('friendly', $allAdjectives);
        $this->assertContains('rebellious', $allAdjectives);
        $this->assertContains('young', $allAdjectives);
    }
}