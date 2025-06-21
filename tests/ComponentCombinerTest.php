<?php

namespace Tests;

use StoryGen\Services\ComponentCombiner;
use StoryGen\Services\ComponentValidator;
use PHPUnit\Framework\TestCase;

class ComponentCombinerTest extends TestCase
{
    /**
     * @var ComponentValidator
     */
    private $validator;

    /**
     * @var ComponentCombiner
     */
    private $combiner;

    /**
     * Set up the test environment
     */
    protected function setUp(): void
    {
        $this->validator = $this->createMock(ComponentValidator::class);
        $this->combiner = new ComponentCombiner($this->validator);
    }

    /**
     * Test combineCharacter method
     */
    public function testCombineCharacter(): void
    {
        // Set up the validator mock to return true
        $this->validator->method('validateCharacter')
            ->willReturn(true);

        // Test combining a character
        $result = $this->combiner->combineCharacter('curious', 'child');
        $this->assertEquals('curious child', $result);
    }

    /**
     * Test combineCharacter method with invalid combination
     */
    public function testCombineCharacterInvalid(): void
    {
        // Set up the validator mock to return false
        $this->validator->method('validateCharacter')
            ->willReturn(false);

        // Test combining an invalid character
        $result = $this->combiner->combineCharacter('ancient', 'child');
        $this->assertNull($result);
    }

    /**
     * Test combineSetting method
     */
    public function testCombineSetting(): void
    {
        // Set up the validator mock to return true
        $this->validator->method('validateSetting')
            ->willReturn(true);

        // Test combining a setting
        $result = $this->combiner->combineSetting('enchanted', 'forest');
        $this->assertEquals('enchanted forest', $result);
    }

    /**
     * Test combineSetting method with invalid combination
     */
    public function testCombineSettingInvalid(): void
    {
        // Set up the validator mock to return false
        $this->validator->method('validateSetting')
            ->willReturn(false);

        // Test combining an invalid setting
        $result = $this->combiner->combineSetting('underwater', 'city');
        $this->assertNull($result);
    }

    /**
     * Test combineEvent method
     */
    public function testCombineEvent(): void
    {
        // Set up the validator mock to return true
        $this->validator->method('validateEvent')
            ->willReturn(true);

        // Test combining an event without a modifier
        $result = $this->combiner->combineEvent('finds', 'a treasure map');
        $this->assertEquals('finds a treasure map', $result);

        // Test combining an event with a modifier
        $result = $this->combiner->combineEvent('finds', 'a treasure map', 'accidentally');
        $this->assertEquals('finds accidentally a treasure map', $result);
    }

    /**
     * Test combineEvent method with invalid combination
     */
    public function testCombineEventInvalid(): void
    {
        // Set up the validator mock to return false
        $this->validator->method('validateEvent')
            ->willReturn(false);

        // Test combining an invalid event
        $result = $this->combiner->combineEvent('finds', 'a treasure map');
        $this->assertNull($result);
    }

    /**
     * Test combineObject method
     */
    public function testCombineObject(): void
    {
        // Set up the validator mock to return true
        $this->validator->method('validateObject')
            ->willReturn(true);

        // Test combining an object
        $result = $this->combiner->combineObject('magic', 'wand');
        $this->assertEquals('magic wand', $result);
    }

    /**
     * Test combineObject method with invalid combination
     */
    public function testCombineObjectInvalid(): void
    {
        // Set up the validator mock to return false
        $this->validator->method('validateObject')
            ->willReturn(false);

        // Test combining an invalid object
        $result = $this->combiner->combineObject('talking', 'wand');
        $this->assertNull($result);
    }

    /**
     * Test generateRandomCharacter method
     */
    public function testGenerateRandomCharacter(): void
    {
        // Set up the validator mock to return true
        $this->validator->method('validateCharacter')
            ->willReturn(true);

        // Test generating a random character
        $adjectives = ['curious', 'friendly', 'brave'];
        $nouns = ['child', 'robot', 'explorer'];
        $result = $this->combiner->generateRandomCharacter($adjectives, $nouns);

        // Check that the result is a string and contains one of the adjectives and one of the nouns
        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/^(curious|friendly|brave) (child|robot|explorer)$/', $result);
    }

    /**
     * Test generateRandomCharacter method with empty arrays
     */
    public function testGenerateRandomCharacterEmptyArrays(): void
    {
        // Test generating a random character with empty arrays
        $result = $this->combiner->generateRandomCharacter([], []);
        $this->assertNull($result);
    }

    /**
     * Test generateRandomSetting method
     */
    public function testGenerateRandomSetting(): void
    {
        // Set up the validator mock to return true
        $this->validator->method('validateSetting')
            ->willReturn(true);

        // Test generating a random setting
        $adjectives = ['enchanted', 'hidden', 'crystal'];
        $nouns = ['forest', 'cave', 'island'];
        $result = $this->combiner->generateRandomSetting($adjectives, $nouns);

        // Check that the result is a string and contains one of the adjectives and one of the nouns
        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/^(enchanted|hidden|crystal) (forest|cave|island)$/', $result);
    }

    /**
     * Test generateRandomEvent method
     */
    public function testGenerateRandomEvent(): void
    {
        // Set up the validator mock to return true
        $this->validator->method('validateEvent')
            ->willReturn(true);

        // Test generating a random event without modifiers
        $verbs = ['finds', 'discovers', 'creates'];
        $objects = ['a treasure map', 'a secret door', 'a new friend'];
        $result = $this->combiner->generateRandomEvent($verbs, $objects, []);

        // Check that the result is a string and contains one of the verbs and one of the objects
        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/^(finds|discovers|creates) (a treasure map|a secret door|a new friend)$/', $result);

        // Test generating a random event with modifiers
        $modifiers = ['accidentally', 'suddenly', 'magically'];
        $result = $this->combiner->generateRandomEvent($verbs, $objects, $modifiers, 1.0); // 100% chance of using a modifier

        // Check that the result is a string and contains one of the verbs, one of the modifiers, and one of the objects
        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/^(finds|discovers|creates) (accidentally|suddenly|magically) (a treasure map|a secret door|a new friend)$/', $result);
    }

    /**
     * Test generateRandomObject method
     */
    public function testGenerateRandomObject(): void
    {
        // Set up the validator mock to return true
        $this->validator->method('validateObject')
            ->willReturn(true);

        // Test generating a random object
        $qualities = ['magic', 'glowing', 'ancient'];
        $items = ['wand', 'stone', 'book'];
        $result = $this->combiner->generateRandomObject($qualities, $items);

        // Check that the result is a string and contains one of the qualities and one of the items
        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/^(magic|glowing|ancient) (wand|stone|book)$/', $result);
    }

    /**
     * Test generateRandomElements method
     */
    public function testGenerateRandomElements(): void
    {
        // Set up the validator mock to return true for all validation methods
        $this->validator->method('validateCharacter')->willReturn(true);
        $this->validator->method('validateSetting')->willReturn(true);
        $this->validator->method('validateEvent')->willReturn(true);
        $this->validator->method('validateObject')->willReturn(true);

        // Test generating random characters
        $components = [
            'adjectives' => ['curious', 'friendly', 'brave'],
            'nouns' => ['child', 'robot', 'explorer']
        ];
        $result = $this->combiner->generateRandomElements('character', $components, 2);

        // Check that the result is an array of 2 strings
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        foreach ($result as $element) {
            $this->assertIsString($element);
            $this->assertMatchesRegularExpression('/^(curious|friendly|brave) (child|robot|explorer)$/', $element);
        }

        // Test generating random settings
        $components = [
            'adjectives' => ['enchanted', 'hidden', 'crystal'],
            'nouns' => ['forest', 'cave', 'island']
        ];
        $result = $this->combiner->generateRandomElements('setting', $components, 2);

        // Check that the result is an array of 2 strings
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        foreach ($result as $element) {
            $this->assertIsString($element);
            $this->assertMatchesRegularExpression('/^(enchanted|hidden|crystal) (forest|cave|island)$/', $element);
        }

        // Test generating random events
        $components = [
            'verbs' => ['finds', 'discovers', 'creates'],
            'objects' => ['a treasure map', 'a secret door', 'a new friend'],
            'modifiers' => ['accidentally', 'suddenly', 'magically']
        ];
        $result = $this->combiner->generateRandomElements('event', $components, 2);

        // Check that the result is an array of 2 strings
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        foreach ($result as $element) {
            $this->assertIsString($element);
            // The event might or might not have a modifier, so we need to check both patterns
            $this->assertTrue(
                preg_match('/^(finds|discovers|creates) (a treasure map|a secret door|a new friend)$/', $element) === 1 ||
                preg_match('/^(finds|discovers|creates) (accidentally|suddenly|magically) (a treasure map|a secret door|a new friend)$/', $element) === 1
            );
        }

        // Test generating random objects
        $components = [
            'qualities' => ['magic', 'glowing', 'ancient'],
            'items' => ['wand', 'stone', 'book']
        ];
        $result = $this->combiner->generateRandomElements('object', $components, 2);

        // Check that the result is an array of 2 strings
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        foreach ($result as $element) {
            $this->assertIsString($element);
            $this->assertMatchesRegularExpression('/^(magic|glowing|ancient) (wand|stone|book)$/', $element);
        }
    }
}
