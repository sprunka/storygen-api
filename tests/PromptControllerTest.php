<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use StoryGen\Controllers\PromptController;
use StoryGen\Services\PromptGenerator;
use StoryGen\Services\SeedLoader;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class PromptControllerTest extends TestCase
{
    private $promptController;
    private $request;
    private $response;
    private $stream;

    protected function setUp(): void
    {
        // Create mock objects
        $seedLoader = $this->createMock(SeedLoader::class);
        $promptGenerator = $this->createMock(PromptGenerator::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->stream = $this->createMock(StreamInterface::class);

        // Configure mocks
        $seedLoader->method('getPromptElementsByAgeGroup')->willReturn([
            'characters' => ['test character'],
            'settings' => ['test setting'],
            'events' => ['test event'],
            'objects' => ['test object']
        ]);

        $seedLoader->method('getAllElementsByType')->willReturn(['test element']);

        $promptGenerator->method('generatePrompt')->willReturn([
            'character' => 'test character',
            'setting' => 'test setting',
            'event' => 'test event',
            'object' => 'test object'
        ]);

        $promptGenerator->method('getRandomCard')->willReturn([
            'tableTitle' => 'Character Card',
            'character' => 'test card'
        ]);

        $promptGenerator->method('generateDiceRolls')->willReturn([
            'tableTitle' => 'Dice Rolls',
            'cards' => [
                'character' => 'test character',
                'setting' => 'test setting',
                'event' => 'test event'
            ]
        ]);

        $this->response->method('getBody')->willReturn($this->stream);
        $this->response->method('withHeader')->willReturnSelf();
        $this->response->method('withStatus')->willReturnSelf();

        // Create controller with mocked dependencies
        $this->promptController = new PromptController($promptGenerator);
    }

    public function testGetPrompt(): void
    {
        // Configure request mock
        $this->request->method('getQueryParams')->willReturn([]);

        // Set up expectation for write method
        $expectedJson = json_encode([
            'character' => 'test character',
            'setting' => 'test setting',
            'event' => 'test event',
            'object' => 'test object'
        ], JSON_PRETTY_PRINT);
        $this->stream->expects($this->once())->method('write')->with($expectedJson);

        // Test the controller method
        $response = $this->promptController->getPrompt($this->request, $this->response);

        // Assert that the response is returned
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testGetPromptWithAgeGroup(): void
    {
        // Configure request mock with age_group parameter
        $this->request->method('getQueryParams')->willReturn(['age_group' => 'kids']);

        // Set up expectation for write method
        $expectedJson = json_encode([
            'character' => 'test character',
            'setting' => 'test setting',
            'event' => 'test event',
            'object' => 'test object'
        ], JSON_PRETTY_PRINT);
        $this->stream->expects($this->once())->method('write')->with($expectedJson);

        // Test the controller method
        $response = $this->promptController->getPrompt($this->request, $this->response);

        // Assert that the response is returned
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testGetCard(): void
    {
        // Configure request mock with type parameter
        $this->request->method('getQueryParams')->willReturn(['type' => 'character']);

        // Set up expectation for write method
        $expectedJson = json_encode([
            'tableTitle' => 'Character Card',
            'character' => 'test card'
        ], JSON_PRETTY_PRINT);
        $this->stream->expects($this->once())->method('write')->with($expectedJson);

        // Test the controller method
        $response = $this->promptController->getCard($this->request, $this->response);

        // Assert that the response is returned
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testGetCardWithoutType(): void
    {
        // Configure request mock without type parameter
        $this->request->method('getQueryParams')->willReturn([]);

        // Set up expectations for write method and withStatus method
        $expectedJson = json_encode(['error' => 'Type parameter is required'], JSON_PRETTY_PRINT);
        $this->stream->expects($this->once())->method('write')->with($expectedJson);
        $this->response->expects($this->once())->method('withStatus')->with(400);

        // Test the controller method
        $response = $this->promptController->getCard($this->request, $this->response);

        // Assert that the response is returned
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testGetDiceRolls(): void
    {
        // Configure request mock with count parameter
        $this->request->method('getQueryParams')->willReturn(['count' => '3']);

        // Set up expectation for write method
        $expectedJson = json_encode([
            'tableTitle' => 'Dice Rolls',
            'cards' => [
                'character' => 'test character',
                'setting' => 'test setting',
                'event' => 'test event'
            ]
        ], JSON_PRETTY_PRINT);
        $this->stream->expects($this->once())->method('write')->with($expectedJson);

        // Test the controller method
        $response = $this->promptController->getDiceRolls($this->request, $this->response);

        // Assert that the response is returned
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testGetDiceRollsWithInvalidCount(): void
    {
        // Configure request mock with invalid count parameter
        $this->request->method('getQueryParams')->willReturn(['count' => '20']);

        // Set up expectations for write method and withStatus method
        $expectedJson = json_encode(['error' => 'Count must be between 1 and 10'], JSON_PRETTY_PRINT);
        $this->stream->expects($this->once())->method('write')->with($expectedJson);
        $this->response->expects($this->once())->method('withStatus')->with(400);

        // Test the controller method
        $response = $this->promptController->getDiceRolls($this->request, $this->response);

        // Assert that the response is returned
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
