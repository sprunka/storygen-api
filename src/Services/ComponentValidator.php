<?php

namespace App\Services;

/**
 * Validator for component combinations
 * 
 * This service validates combinations of components to ensure they are
 * grammatically correct and contextually appropriate.
 */
class ComponentValidator
{
    /**
     * @var array Compatibility rules for components
     */
    private array $compatibilityRules;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initializeCompatibilityRules();
    }

    /**
     * Initialize compatibility rules
     *
     * @return void
     */
    private function initializeCompatibilityRules(): void
    {
        // Initialize compatibility rules
        // These rules define which components can be combined with each other
        $this->compatibilityRules = [
            'character' => [
                // Some adjectives only apply to animate objects
                'animate_only' => [
                    'curious', 'friendly', 'brave', 'shy', 'mischievous',
                    'rebellious', 'reluctant', 'dreamy', 'haunted', 'washed-up',
                    'burned-out'
                ],
                // Some adjectives only apply to inanimate objects
                'inanimate_only' => [
                    'ancient', 'broken', 'rusty', 'shattered', 'crumbling'
                ],
                // Nouns that are animate
                'animate' => [
                    'child', 'robot', 'animal', 'explorer', 'fairy',
                    'pirate', 'witch', 'squirrel', 'dragon', 'alien',
                    'mermaid', 'friend', 'kid', 'penguin', 'guardian',
                    'puppy', 'monster', 'unicorn', 'duck', 'magicians',
                    'wizard', 'knight', 'alchemist', 'teenager', 'inventor',
                    'musician', 'detective', 'student', 'hero', 'genius',
                    'actor', 'witch', 'journalist', 'gamer', 'hacker',
                    'activist', 'outcast', 'poet', 'artist', 'transfer',
                    'rival', 'lead', 'duo', 'prodigy', 'teen',
                    'podcaster', 'writer', 'librarian', 'detective', 'spy',
                    'artist', 'musician', 'teacher', 'investigator', 'hunter',
                    'war vet', 'scientist', 'immortal', 'archaeologist', 'agent',
                    'diver', 'magician', 'anthropologist', 'analyst', 'teller',
                    'priest', 'writer'
                ]
            ],
            'setting' => [
                // Some adjectives only apply to natural settings
                'natural_only' => [
                    'enchanted', 'underwater', 'floating', 'hidden', 'crystal',
                    'rainbow', 'marshmallow', 'chocolate', 'frozen', 'foggy'
                ],
                // Some adjectives only apply to man-made settings
                'man_made_only' => [
                    'abandoned', 'haunted', 'underground', 'virtual reality', 'cyberpunk',
                    'luxury', 'silent', 'burned', 'dystopian', 'war-ravaged'
                ],
                // Nouns that are natural settings
                'natural' => [
                    'forest', 'kingdom', 'island', 'jungle', 'mountain',
                    'river', 'garden', 'desert', 'sea', 'cave',
                    'volcano'
                ],
                // Nouns that are man-made settings
                'man_made' => [
                    'station', 'school', 'city', 'treehouse', 'factory',
                    'workshop', 'village', 'zoo', 'library', 'playground',
                    'attic', 'world', 'maze', 'house', 'mall',
                    'camp', 'town', 'lab', 'rink', 'skatepark',
                    'arcade', 'tunnel', 'fort', 'set', 'convention',
                    'station', 'ruins', 'circle', 'fair', 'room',
                    'amusement park', 'colony', 'capital', 'subway', 'base',
                    'casino', 'marketplace', 'prison', 'junkyard', 'alley',
                    'bunker', 'monastery', 'ship', 'library'
                ]
            ],
            'event' => [
                // Some verbs require animate subjects
                'animate_subject_only' => [
                    'discovers', 'makes', 'learns', 'solves', 'rescues',
                    'saves', 'rides', 'transforms', 'flies', 'hears',
                    'helps', 'builds', 'becomes', 'paints', 'talks',
                    'wins', 'grows', 'befriends', 'stops', 'clones',
                    'uncovers', 'competes', 'stands up', 'falls', 'witnesses',
                    'runs away', 'crashes', 'fakes', 'writes', 'builds',
                    'trains', 'starts', 'leads', 'connects', 'makes',
                    'defies', 'saves', 'joins', 'unlocks', 'questions',
                    'encounters', 'receives', 'witnesses', 'wakes up', 'triggers',
                    'meets', 'discovers', 'exposes', 'inherits', 'revives',
                    'unearths', 'faces', 'escapes', 'takes', 'steals',
                    'enters', 'fights', 'awakens'
                ],
                // Some verbs can have inanimate subjects
                'inanimate_subject_ok' => [
                    'finds', 'gets', 'opens', 'reverses', 'is trapped',
                    'is betrayed', 'loses', 'falls'
                ]
            ],
            'object' => [
                // Some qualities only apply to animate objects
                'animate_only' => [
                    'talking', 'singing', 'laughing', 'friendly', 'ghostly',
                    'haunted'
                ],
                // Some qualities only apply to inanimate objects
                'inanimate_only' => [
                    'magic', 'special', 'glowing', 'flying', 'invisibility',
                    'robotic', 'bouncing', 'enchanted', 'mini', 'self-writing',
                    'teleporting', 'star-catching', 'mood-changing', 'puzzle', 'growing',
                    'bubble-blowing', 'secret', 'fizzy', 'floating', 'time-freezing',
                    'mysterious', 'vintage', 'smart', 'family', 'rare',
                    'glowing graffiti', 'with a secret', 'with hidden files', 'retro', 'cybernetic',
                    'audio', 'missing student', 'tattoo', 'encrypted', 'trick',
                    'school', 'old', 'outdated', 'painted', 'broken',
                    'flash', 'tattered', 'symbolic', 'antique', 'enchanted',
                    'experimental', 'cursed', 'holographic', 'time-slowing', 'strange',
                    'glass', 'bloodstained', 'weather-controlling', 'cybernetic', 'ancient',
                    'blackout-inducing', 'soul trap', 'burning', 'light-reactive'
                ],
                // Items that are animate
                'animate' => [
                    'cat', 'teddy bear', 'animal', 'friend', 'puppy',
                    'monster', 'unicorn', 'duck', 'magicians', 'wizard',
                    'knight', 'alchemist'
                ]
            ]
        ];
    }

    /**
     * Validate a character combination
     *
     * @param string $adjective Adjective component
     * @param string $noun Noun component
     * @return bool True if the combination is valid, false otherwise
     */
    public function validateCharacter(string $adjective, string $noun): bool
    {
        // Check if the adjective is animate-only and the noun is not animate
        if (in_array($adjective, $this->compatibilityRules['character']['animate_only']) &&
            !in_array($noun, $this->compatibilityRules['character']['animate'])) {
            return false;
        }

        // Check if the adjective is inanimate-only and the noun is animate
        if (in_array($adjective, $this->compatibilityRules['character']['inanimate_only']) &&
            in_array($noun, $this->compatibilityRules['character']['animate'])) {
            return false;
        }

        return true;
    }

    /**
     * Validate a setting combination
     *
     * @param string $adjective Adjective component
     * @param string $noun Noun component
     * @return bool True if the combination is valid, false otherwise
     */
    public function validateSetting(string $adjective, string $noun): bool
    {
        // Check if the adjective is natural-only and the noun is not natural
        if (in_array($adjective, $this->compatibilityRules['setting']['natural_only']) &&
            !in_array($noun, $this->compatibilityRules['setting']['natural'])) {
            return false;
        }

        // Check if the adjective is man-made-only and the noun is not man-made
        if (in_array($adjective, $this->compatibilityRules['setting']['man_made_only']) &&
            !in_array($noun, $this->compatibilityRules['setting']['man_made'])) {
            return false;
        }

        return true;
    }

    /**
     * Validate an event combination
     *
     * @param string $verb Verb component
     * @param string $object Object component
     * @param string|null $modifier Optional modifier component
     * @return bool True if the combination is valid, false otherwise
     */
    public function validateEvent(string $verb, string $object, ?string $modifier = null): bool
    {
        // For now, we'll assume all combinations are valid
        // In a more sophisticated implementation, we would check for semantic coherence
        return true;
    }

    /**
     * Validate an object combination
     *
     * @param string $quality Quality component
     * @param string $item Item component
     * @return bool True if the combination is valid, false otherwise
     */
    public function validateObject(string $quality, string $item): bool
    {
        // Check if the quality is animate-only and the item is not animate
        if (in_array($quality, $this->compatibilityRules['object']['animate_only']) &&
            !in_array($item, $this->compatibilityRules['object']['animate'])) {
            return false;
        }

        // Check if the quality is inanimate-only and the item is animate
        if (in_array($quality, $this->compatibilityRules['object']['inanimate_only']) &&
            in_array($item, $this->compatibilityRules['object']['animate'])) {
            return false;
        }

        return true;
    }

    /**
     * Validate a complete prompt
     *
     * @param array $prompt Prompt to validate
     * @return bool True if the prompt is valid, false otherwise
     */
    public function validatePrompt(array $prompt): bool
    {
        // Validate character
        if (isset($prompt['character']['adjective']) && isset($prompt['character']['noun'])) {
            if (!$this->validateCharacter($prompt['character']['adjective'], $prompt['character']['noun'])) {
                return false;
            }
        }

        // Validate setting
        if (isset($prompt['setting']['adjective']) && isset($prompt['setting']['noun'])) {
            if (!$this->validateSetting($prompt['setting']['adjective'], $prompt['setting']['noun'])) {
                return false;
            }
        }

        // Validate event
        if (isset($prompt['event']['verb']) && isset($prompt['event']['object'])) {
            $modifier = $prompt['event']['modifier'] ?? null;
            if (!$this->validateEvent($prompt['event']['verb'], $prompt['event']['object'], $modifier)) {
                return false;
            }
        }

        // Validate object
        if (isset($prompt['object']['quality']) && isset($prompt['object']['item'])) {
            if (!$this->validateObject($prompt['object']['quality'], $prompt['object']['item'])) {
                return false;
            }
        }

        return true;
    }
}