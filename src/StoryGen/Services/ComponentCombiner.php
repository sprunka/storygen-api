<?php

namespace StoryGen\Services;

/**
 * Service for combining modular components into complete elements
 * 
 * This service is responsible for combining modular components (adjectives, nouns, verbs, etc.)
 * into complete elements (characters, settings, events, objects) with validation to ensure
 * the combinations make sense.
 */
class ComponentCombiner
{
    /**
     * @var ComponentValidator
     */
    private ComponentValidator $validator;

    /**
     * Constructor
     *
     * @param ComponentValidator $validator
     */
    public function __construct(ComponentValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Combine character components into a complete character
     *
     * @param string $adjective Adjective component
     * @param string $noun Noun component
     * @return string|null Complete character or null if the combination is invalid
     */
    public function combineCharacter(string $adjective, string $noun): ?string
    {
        // Validate the combination
        if (!$this->validator->validateCharacter($adjective, $noun)) {
            return null;
        }

        return $adjective . ' ' . $noun;
    }

    /**
     * Combine setting components into a complete setting
     *
     * @param string $adjective Adjective component
     * @param string $noun Noun component
     * @return string|null Complete setting or null if the combination is invalid
     */
    public function combineSetting(string $adjective, string $noun): ?string
    {
        // Validate the combination
        if (!$this->validator->validateSetting($adjective, $noun)) {
            return null;
        }

        return $adjective . ' ' . $noun;
    }

    /**
     * Combine event components into a complete event
     *
     * @param string $verb Verb component
     * @param string $object Object component
     * @param string|null $modifier Optional modifier component
     * @return string|null Complete event or null if the combination is invalid
     */
    public function combineEvent(string $verb, string $object, ?string $modifier = null): ?string
    {
        // Validate the combination
        if (!$this->validator->validateEvent($verb, $object, $modifier)) {
            return null;
        }

        // If a modifier is provided, insert it between the verb and object
        if ($modifier !== null) {
            return $verb . ' ' . $modifier . ' ' . $object;
        }

        return $verb . ' ' . $object;
    }

    /**
     * Combine object components into a complete object
     *
     * @param string $quality Quality component
     * @param string $item Item component
     * @return string|null Complete object or null if the combination is invalid
     */
    public function combineObject(string $quality, string $item): ?string
    {
        // Validate the combination
        if (!$this->validator->validateObject($quality, $item)) {
            return null;
        }

        return $quality . ' ' . $item;
    }

    /**
     * Generate a random valid character from arrays of adjectives and nouns
     *
     * @param array $adjectives Array of adjective components
     * @param array $nouns Array of noun components
     * @param int $maxAttempts Maximum number of attempts to find a valid combination
     * @return string|null Random valid character or null if no valid combination could be found
     */
    public function generateRandomCharacter(array $adjectives, array $nouns, int $maxAttempts = 10): ?string
    {
        // If either array is empty, return null
        if (empty($adjectives) || empty($nouns)) {
            return null;
        }

        // Try to find a valid combination
        for ($i = 0; $i < $maxAttempts; $i++) {
            $adjective = $adjectives[array_rand($adjectives)];
            $noun = $nouns[array_rand($nouns)];
            
            $character = $this->combineCharacter($adjective, $noun);
            if ($character !== null) {
                return $character;
            }
        }

        // If no valid combination could be found, return null
        return null;
    }

    /**
     * Generate a random valid setting from arrays of adjectives and nouns
     *
     * @param array $adjectives Array of adjective components
     * @param array $nouns Array of noun components
     * @param int $maxAttempts Maximum number of attempts to find a valid combination
     * @return string|null Random valid setting or null if no valid combination could be found
     */
    public function generateRandomSetting(array $adjectives, array $nouns, int $maxAttempts = 10): ?string
    {
        // If either array is empty, return null
        if (empty($adjectives) || empty($nouns)) {
            return null;
        }

        // Try to find a valid combination
        for ($i = 0; $i < $maxAttempts; $i++) {
            $adjective = $adjectives[array_rand($adjectives)];
            $noun = $nouns[array_rand($nouns)];
            
            $setting = $this->combineSetting($adjective, $noun);
            if ($setting !== null) {
                return $setting;
            }
        }

        // If no valid combination could be found, return null
        return null;
    }

    /**
     * Generate a random valid event from arrays of verbs, objects, and modifiers
     *
     * @param array $verbs Array of verb components
     * @param array $objects Array of object components
     * @param array $modifiers Array of modifier components (optional)
     * @param float $modifierProbability Probability of including a modifier (0.0 to 1.0)
     * @param int $maxAttempts Maximum number of attempts to find a valid combination
     * @return string|null Random valid event or null if no valid combination could be found
     */
    public function generateRandomEvent(
        array $verbs,
        array $objects,
        array $modifiers = [],
        float $modifierProbability = 0.3,
        int $maxAttempts = 10
    ): ?string {
        // If either verbs or objects is empty, return null
        if (empty($verbs) || empty($objects)) {
            return null;
        }

        // Try to find a valid combination
        for ($i = 0; $i < $maxAttempts; $i++) {
            $verb = $verbs[array_rand($verbs)];
            $object = $objects[array_rand($objects)];
            $modifier = null;
            
            // Randomly include a modifier based on the probability
            if (!empty($modifiers) && mt_rand() / mt_getrandmax() < $modifierProbability) {
                $modifier = $modifiers[array_rand($modifiers)];
            }
            
            $event = $this->combineEvent($verb, $object, $modifier);
            if ($event !== null) {
                return $event;
            }
        }

        // If no valid combination could be found, return null
        return null;
    }

    /**
     * Generate a random valid object from arrays of qualities and items
     *
     * @param array $qualities Array of quality components
     * @param array $items Array of item components
     * @param int $maxAttempts Maximum number of attempts to find a valid combination
     * @return string|null Random valid object or null if no valid combination could be found
     */
    public function generateRandomObject(array $qualities, array $items, int $maxAttempts = 10): ?string
    {
        // If either array is empty, return null
        if (empty($qualities) || empty($items)) {
            return null;
        }

        // Try to find a valid combination
        for ($i = 0; $i < $maxAttempts; $i++) {
            $quality = $qualities[array_rand($qualities)];
            $item = $items[array_rand($items)];
            
            $object = $this->combineObject($quality, $item);
            if ($object !== null) {
                return $object;
            }
        }

        // If no valid combination could be found, return null
        return null;
    }

    /**
     * Generate multiple random valid elements of a specific type
     *
     * @param string $type Element type (character, setting, event, object)
     * @param array $components Array of component arrays
     * @param int $count Number of elements to generate
     * @param int $maxAttempts Maximum number of attempts to find a valid combination
     * @return array Array of random valid elements
     */
    public function generateRandomElements(string $type, array $components, int $count = 1, int $maxAttempts = 10): array
    {
        $elements = [];

        // Generate the specified number of elements
        for ($i = 0; $i < $count; $i++) {
            $element = null;

            switch ($type) {
                case 'character':
                    $element = $this->generateRandomCharacter(
                        $components['adjectives'] ?? [],
                        $components['nouns'] ?? [],
                        $maxAttempts
                    );
                    break;
                case 'setting':
                    $element = $this->generateRandomSetting(
                        $components['adjectives'] ?? [],
                        $components['nouns'] ?? [],
                        $maxAttempts
                    );
                    break;
                case 'event':
                    $element = $this->generateRandomEvent(
                        $components['verbs'] ?? [],
                        $components['objects'] ?? [],
                        $components['modifiers'] ?? [],
                        0.3,
                        $maxAttempts
                    );
                    break;
                case 'object':
                    $element = $this->generateRandomObject(
                        $components['qualities'] ?? [],
                        $components['items'] ?? [],
                        $maxAttempts
                    );
                    break;
            }

            if ($element !== null) {
                $elements[] = $element;
            }
        }

        return $elements;
    }
}