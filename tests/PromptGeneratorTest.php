<?php

namespace Tests;

use StoryGen\Services\ComponentCombiner;
use StoryGen\Services\ComponentValidator;
use StoryGen\Services\PromptGenerator;
use StoryGen\Services\SeedLoader;
use PHPUnit\Framework\TestCase;

class PromptGeneratorTest extends TestCase
{
    /**
     * @var SeedLoader
     */
    private $seedLoader;

    /**
     * @var ComponentValidator
     */
    private $validator;

    /**
     * @var ComponentCombiner
     */
    private $combiner;

    /**
     * @var PromptGenerator
     */
    private $generator;

    /**
     * Set up the test environment
     */
    protected function setUp(): void
    {
        $this->seedLoader = $this->createMock(SeedLoader::class);
        $this->validator = $this->createMock(ComponentValidator::class);
        $this->combiner = $this->createMock(ComponentCombiner::class);
        $this->generator = new PromptGenerator($this->seedLoader, $this->combiner);
    }

    /**
     * Test generatePrompt method with original seed data
     */
    public function testGeneratePromptWithOriginalSeedData(): void
    {
        // Create a generator without the component combiner
        $generator = new PromptGenerator($this->seedLoader);

        // Set up the seed loader mock to return test data
        $this->seedLoader->method('getPromptElementsByAgeGroup')
            ->willReturn([
                'characters' => ['curious child', 'friendly robot', 'brave explorer'],
                'settings' => ['enchanted forest', 'space station', 'underwater kingdom'],
                'events' => ['finds a treasure map', 'discovers a secret door', 'makes a new friend'],
                'objects' => ['magic wand', 'talking toy', 'special key']
            ]);

        // Test generating a prompt with a specific count
        $prompt = $generator->generatePrompt('kids', 2);

        // Check that the prompt has the expected structure
        $this->assertIsArray($prompt);
        $this->assertArrayHasKey('character', $prompt);
        $this->assertArrayHasKey('setting', $prompt);
        $this->assertArrayHasKey('event', $prompt);
        $this->assertArrayHasKey('object', $prompt);

        // Check that each category has the expected number of elements
        $this->assertCount(2, $prompt['character']);
        $this->assertCount(2, $prompt['setting']);
        $this->assertCount(2, $prompt['event']);
        $this->assertCount(2, $prompt['object']);

        // Check that each element is one of the expected values
        foreach ($prompt['character'] as $character) {
            $this->assertContains($character, ['curious child', 'friendly robot', 'brave explorer']);
        }
        foreach ($prompt['setting'] as $setting) {
            $this->assertContains($setting, ['enchanted forest', 'space station', 'underwater kingdom']);
        }
        foreach ($prompt['event'] as $event) {
            $this->assertContains($event, ['finds a treasure map', 'discovers a secret door', 'makes a new friend']);
        }
        foreach ($prompt['object'] as $object) {
            $this->assertContains($object, ['magic wand', 'talking toy', 'special key']);
        }
    }

    /**
     * Test generatePrompt method with modular seed data
     */
    public function testGeneratePromptWithModularSeedData(): void
    {
        // Set up the seed loader mock to return test data
        $this->seedLoader->method('getModularComponents')
            ->willReturnCallback(function ($ageGroup, $category, $componentType) {
                switch ($category) {
                    case 'characters':
                        if ($componentType === 'adjectives') {
                            return ['curious', 'friendly', 'brave'];
                        } elseif ($componentType === 'nouns') {
                            return ['child', 'robot', 'explorer'];
                        }
                        break;
                    case 'settings':
                        if ($componentType === 'adjectives') {
                            return ['enchanted', 'space', 'underwater'];
                        } elseif ($componentType === 'nouns') {
                            return ['forest', 'station', 'kingdom'];
                        }
                        break;
                    case 'events':
                        if ($componentType === 'verbs') {
                            return ['finds', 'discovers', 'makes'];
                        } elseif ($componentType === 'objects') {
                            return ['a treasure map', 'a secret door', 'a new friend'];
                        } elseif ($componentType === 'modifiers') {
                            return ['accidentally', 'suddenly', 'magically'];
                        }
                        break;
                    case 'objects':
                        if ($componentType === 'qualities') {
                            return ['magic', 'talking', 'special'];
                        } elseif ($componentType === 'items') {
                            return ['wand', 'toy', 'key'];
                        }
                        break;
                }
                return [];
            });

        // Set up the component combiner mock to return test data
        $this->combiner->method('generateRandomElements')
            ->willReturnCallback(function ($type, $components, $count) {
                $result = [];
                for ($i = 0; $i < $count; $i++) {
                    switch ($type) {
                        case 'character':
                            $result[] = $components['adjectives'][0] . ' ' . $components['nouns'][0];
                            break;
                        case 'setting':
                            $result[] = $components['adjectives'][0] . ' ' . $components['nouns'][0];
                            break;
                        case 'event':
                            $result[] = $components['verbs'][0] . ' ' . $components['objects'][0];
                            break;
                        case 'object':
                            $result[] = $components['qualities'][0] . ' ' . $components['items'][0];
                            break;
                    }
                }
                return $result;
            });

        // Test generating a prompt with a specific count
        $prompt = $this->generator->generatePrompt('kids', 2);

        // Check that the prompt has the expected structure
        $this->assertIsArray($prompt);
        $this->assertArrayHasKey('character', $prompt);
        $this->assertArrayHasKey('setting', $prompt);
        $this->assertArrayHasKey('event', $prompt);
        $this->assertArrayHasKey('object', $prompt);

        // Check that each category has the expected number of elements
        $this->assertCount(2, $prompt['character']);
        $this->assertCount(2, $prompt['setting']);
        $this->assertCount(2, $prompt['event']);
        $this->assertCount(2, $prompt['object']);

        // Check that each element has the expected format
        foreach ($prompt['character'] as $character) {
            $this->assertEquals('curious child', $character);
        }
        foreach ($prompt['setting'] as $setting) {
            $this->assertEquals('enchanted forest', $setting);
        }
        foreach ($prompt['event'] as $event) {
            $this->assertEquals('finds a treasure map', $event);
        }
        foreach ($prompt['object'] as $object) {
            $this->assertEquals('magic wand', $object);
        }
    }

    /**
     * Test getRandomCard method with original seed data
     */
    public function testGetRandomCardWithOriginalSeedData(): void
    {
        // Create a generator without the component combiner
        $generator = new PromptGenerator($this->seedLoader);

        // Set up the seed loader mock to return test data
        $this->seedLoader->method('getAllElementsByType')
            ->willReturnCallback(function ($type) {
                switch ($type) {
                    case 'characters':
                        return ['curious child', 'friendly robot', 'brave explorer'];
                    case 'settings':
                        return ['enchanted forest', 'space station', 'underwater kingdom'];
                    case 'events':
                        return ['finds a treasure map', 'discovers a secret door', 'makes a new friend'];
                    case 'objects':
                        return ['magic wand', 'talking toy', 'special key'];
                }
                return [];
            });

        // Test getting a random card for each type
        $types = ['character', 'setting', 'event', 'object'];
        foreach ($types as $type) {
            $card = $generator->getRandomCard($type);

            // Check that the card has the expected structure
            $this->assertIsArray($card);
            $this->assertArrayHasKey('tableTitle', $card);
            $this->assertArrayHasKey($type, $card);

            // Check that the table title is correct
            $this->assertEquals(ucfirst($type) . ' Card', $card['tableTitle']);

            // Check that the element is one of the expected values
            switch ($type) {
                case 'character':
                    $this->assertContains($card[$type], ['curious child', 'friendly robot', 'brave explorer']);
                    break;
                case 'setting':
                    $this->assertContains($card[$type], ['enchanted forest', 'space station', 'underwater kingdom']);
                    break;
                case 'event':
                    $this->assertContains($card[$type], ['finds a treasure map', 'discovers a secret door', 'makes a new friend']);
                    break;
                case 'object':
                    $this->assertContains($card[$type], ['magic wand', 'talking toy', 'special key']);
                    break;
            }
        }
    }

    /**
     * Test getRandomCard method with modular seed data
     */
    public function testGetRandomCardWithModularSeedData(): void
    {
        // Set up the seed loader mock to return test data
        $this->seedLoader->method('getModularComponents')
            ->willReturnCallback(function ($ageGroup, $category, $componentType) {
                switch ($category) {
                    case 'characters':
                        if ($componentType === 'adjectives') {
                            return ['curious', 'friendly', 'brave'];
                        } elseif ($componentType === 'nouns') {
                            return ['child', 'robot', 'explorer'];
                        }
                        break;
                    case 'settings':
                        if ($componentType === 'adjectives') {
                            return ['enchanted', 'space', 'underwater'];
                        } elseif ($componentType === 'nouns') {
                            return ['forest', 'station', 'kingdom'];
                        }
                        break;
                    case 'events':
                        if ($componentType === 'verbs') {
                            return ['finds', 'discovers', 'makes'];
                        } elseif ($componentType === 'objects') {
                            return ['a treasure map', 'a secret door', 'a new friend'];
                        } elseif ($componentType === 'modifiers') {
                            return ['accidentally', 'suddenly', 'magically'];
                        }
                        break;
                    case 'objects':
                        if ($componentType === 'qualities') {
                            return ['magic', 'talking', 'special'];
                        } elseif ($componentType === 'items') {
                            return ['wand', 'toy', 'key'];
                        }
                        break;
                }
                return [];
            });

        // Set up the component combiner mock to return test data
        $this->combiner->method('generateRandomCharacter')
            ->willReturn('curious child');
        $this->combiner->method('generateRandomSetting')
            ->willReturn('enchanted forest');
        $this->combiner->method('generateRandomEvent')
            ->willReturn('finds a treasure map');
        $this->combiner->method('generateRandomObject')
            ->willReturn('magic wand');

        // Test getting a random card for each type
        $types = ['character', 'setting', 'event', 'object'];
        foreach ($types as $type) {
            $card = $this->generator->getRandomCard($type);

            // Check that the card has the expected structure
            $this->assertIsArray($card);
            $this->assertArrayHasKey('tableTitle', $card);
            $this->assertArrayHasKey($type, $card);

            // Check that the table title is correct
            $this->assertEquals(ucfirst($type) . ' Card', $card['tableTitle']);

            // Check that the element has the expected value
            switch ($type) {
                case 'character':
                    $this->assertEquals('curious child', $card[$type]);
                    break;
                case 'setting':
                    $this->assertEquals('enchanted forest', $card[$type]);
                    break;
                case 'event':
                    $this->assertEquals('finds a treasure map', $card[$type]);
                    break;
                case 'object':
                    $this->assertEquals('magic wand', $card[$type]);
                    break;
            }
        }
    }

    /**
     * Test generateDiceRolls method with original seed data
     */
    public function testGenerateDiceRollsWithOriginalSeedData(): void
    {
        // Create a generator without the component combiner
        $generator = new PromptGenerator($this->seedLoader);

        // Set up the seed loader mock to return test data
        $this->seedLoader->method('getAllElementsByType')
            ->willReturnCallback(function ($type) {
                switch ($type) {
                    case 'characters':
                        return ['curious child', 'friendly robot', 'brave explorer'];
                    case 'settings':
                        return ['enchanted forest', 'space station', 'underwater kingdom'];
                    case 'events':
                        return ['finds a treasure map', 'discovers a secret door', 'makes a new friend'];
                    case 'objects':
                        return ['magic wand', 'talking toy', 'special key'];
                }
                return [];
            });

        // Test generating dice rolls
        $diceRolls = $generator->generateDiceRolls(2);

        // Check that the dice rolls have the expected structure
        $this->assertIsArray($diceRolls);
        $this->assertArrayHasKey('tableTitle', $diceRolls);
        $this->assertArrayHasKey('cards', $diceRolls);

        // Check that the table title is correct
        $this->assertEquals('Dice Rolls', $diceRolls['tableTitle']);

        // Check that the cards have the expected structure
        $this->assertIsArray($diceRolls['cards']);
        $this->assertCount(2, $diceRolls['cards']);

        // Check that each card is one of the expected types
        $types = ['character', 'setting', 'event', 'object'];
        foreach ($diceRolls['cards'] as $type => $element) {
            $this->assertContains($type, $types);

            // Check that the element is one of the expected values
            switch ($type) {
                case 'character':
                    $this->assertContains($element, ['curious child', 'friendly robot', 'brave explorer']);
                    break;
                case 'setting':
                    $this->assertContains($element, ['enchanted forest', 'space station', 'underwater kingdom']);
                    break;
                case 'event':
                    $this->assertContains($element, ['finds a treasure map', 'discovers a secret door', 'makes a new friend']);
                    break;
                case 'object':
                    $this->assertContains($element, ['magic wand', 'talking toy', 'special key']);
                    break;
            }
        }
    }

    /**
     * Test generateDiceRolls method with modular seed data
     */
    public function testGenerateDiceRollsWithModularSeedData(): void
    {
        // Set up the seed loader mock to return test data
        $this->seedLoader->method('getModularComponents')
            ->willReturnCallback(function ($ageGroup, $category, $componentType) {
                switch ($category) {
                    case 'characters':
                        if ($componentType === 'adjectives') {
                            return ['curious', 'friendly', 'brave'];
                        } elseif ($componentType === 'nouns') {
                            return ['child', 'robot', 'explorer'];
                        }
                        break;
                    case 'settings':
                        if ($componentType === 'adjectives') {
                            return ['enchanted', 'space', 'underwater'];
                        } elseif ($componentType === 'nouns') {
                            return ['forest', 'station', 'kingdom'];
                        }
                        break;
                    case 'events':
                        if ($componentType === 'verbs') {
                            return ['finds', 'discovers', 'makes'];
                        } elseif ($componentType === 'objects') {
                            return ['a treasure map', 'a secret door', 'a new friend'];
                        } elseif ($componentType === 'modifiers') {
                            return ['accidentally', 'suddenly', 'magically'];
                        }
                        break;
                    case 'objects':
                        if ($componentType === 'qualities') {
                            return ['magic', 'talking', 'special'];
                        } elseif ($componentType === 'items') {
                            return ['wand', 'toy', 'key'];
                        }
                        break;
                }
                return [];
            });

        // Set up the component combiner mock to return test data
        $this->combiner->method('generateRandomCharacter')
            ->willReturn('curious child');
        $this->combiner->method('generateRandomSetting')
            ->willReturn('enchanted forest');
        $this->combiner->method('generateRandomEvent')
            ->willReturn('finds a treasure map');
        $this->combiner->method('generateRandomObject')
            ->willReturn('magic wand');

        // Test generating dice rolls
        $diceRolls = $this->generator->generateDiceRolls(2);

        // Check that the dice rolls have the expected structure
        $this->assertIsArray($diceRolls);
        $this->assertArrayHasKey('tableTitle', $diceRolls);
        $this->assertArrayHasKey('cards', $diceRolls);

        // Check that the table title is correct
        $this->assertEquals('Dice Rolls', $diceRolls['tableTitle']);

        // Check that the cards have the expected structure
        $this->assertIsArray($diceRolls['cards']);
        $this->assertCount(2, $diceRolls['cards']);

        // Check that each card is one of the expected types
        $types = ['character', 'setting', 'event', 'object'];
        foreach ($diceRolls['cards'] as $type => $element) {
            $this->assertContains($type, $types);

            // Check that the element has the expected value
            switch ($type) {
                case 'character':
                    $this->assertEquals('curious child', $element);
                    break;
                case 'setting':
                    $this->assertEquals('enchanted forest', $element);
                    break;
                case 'event':
                    $this->assertEquals('finds a treasure map', $element);
                    break;
                case 'object':
                    $this->assertEquals('magic wand', $element);
                    break;
            }
        }
    }
}
