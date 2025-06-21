# Story Prompts API

A RESTful PHP API service that delivers randomized story prompts by combining elements—characters, settings, events, objects—based on a flexible JSON schema.

## Requirements

- PHP 8.0 or higher
- Composer

## Installation

1. Clone the repository
2. Run `composer install` to install dependencies
3. Run `composer dump-autoload` to generate the autoloader

## Running the Application

```bash
composer start
```

This will start a local development server at http://localhost:8080.

## API Endpoints

### Root Endpoint

```
GET /
```

Returns a JSON message pointing to the OpenAPI documentation.

#### Example Response

```json
[
  "Refer to the documentation at /openapi.json"
]
```

### OpenAPI Documentation

```
GET /openapi.json
```

Returns the OpenAPI 3.0 documentation in JSON format. This documentation provides details on all available API endpoints, including request parameters, response data, and response codes.

### Get a Complete Story Prompt

```
GET /api/prompts
```

Returns a full prompt with a Character + Setting + Event + Object schema.

#### Query Parameters

- `age_group` (optional): Filter by age group (kids, teens, adults). If not specified, elements are randomly selected from a merged array of all age groups.

#### Example Response

```json
{
  "tableTitle": "Story Prompt",
  "character": "shy librarian",
  "setting": "abandoned amusement park",
  "event": "finds a mysterious key",
  "object": "antique locket"
}
```

### Get a Random Card of a Specific Type

```
GET /api/prompts/cards?type=character|setting|event|object
```

Returns a single-element card of the specified type.

#### Query Parameters

- `type` (required): Card type (character, setting, event, object)

#### Example Response

```json
{
  "tableTitle": "Character Card",
  "character": "shy librarian"
}
```

### Get Multiple Random Cards (Dice Rolls)

```
GET /api/prompts/dice?count=3
```

Returns cards as an object to simulate dice rolls.

#### Query Parameters

- `count` (optional): Number of cards to generate (default: 3, max: 4)

#### Example Response

```json
{
  "tableTitle": "Dice Rolls",
  "cards": {
    "character": "shy librarian",
    "setting": "abandoned amusement park",
    "event": "finds a mysterious key"
  }
}
```

## Project Structure

- `public/`: Public-facing files
  - `index.php`: Entry point for the application
- `src/`: Application source code
  - `Controllers/`: API controllers
  - `Models/`: Data models
  - `Services/`: Business logic services
- `data/`: Data files
  - `seed.json`: Seed data for story prompts
- `tests/`: PHPUnit tests

## Running Tests

```bash
vendor/bin/phpunit tests
```

## Extending the Application

### Adding New Seed Data

The seed data is stored in `data/seed.json`. You can add new elements to the existing age groups or add new age groups.

### Adding New Endpoints

1. Create a new method in the appropriate controller
2. Add a new route in `public/index.php`
3. Add Swagger annotations for API documentation
4. Add tests for the new endpoint

## API Documentation

API documentation is generated using Swagger-PHP and is available at the `/openapi.json` endpoint. This endpoint returns the OpenAPI 3.0 documentation in JSON format, which provides details on all available API endpoints, including request parameters, response data, and response codes.

You can also access the documentation by setting up a Swagger UI instance and pointing it to the `/openapi.json` endpoint.
