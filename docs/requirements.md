# Story Prompt API — Requirements and Goals

## Overview

This API is a tool for generating creative writing prompts, inspired by products like Storymatic, Rory’s Story Cubes, and the Story Engine Deck. It’s designed to assist writers, GMs, educators, and creatives in quickly producing random or semi-random story seeds. The prompts are built from modular components: Characters, Settings, Events, and Objects, tailored to specific age groups.

## Functional Requirements

- The API must support prompt generation for the following age groups:
  - Kids (Under 13)
  - Teens (13+, Under 18)
  - Adults (18+)
  - Any (all age groups merged)
- Each generated prompt must contain randomized selections from:
  - Characters
  - Settings
  - Events
  - Objects
- Responses must be returned in structured JSON format.
- API must expose a `GET /prompt` route, with an optional `age_group` query parameter (e.g., `/prompt?age_group=teens`).
  - If `age_group`is not a valid target (`kids`, `teens`, `adults`) the age_group MUST behave the same as if the age_group is `any` (selection from a merged array.)
- The seed data must be stored in a flat-file format (e.g., JSON) and be easy to update or expand. (at this time)
- By default, a prompt should return 1 to 3 entries per category (randomized), but should support an optional `count` query parameter to override this.

## Non-Functional Requirements

- The API must be built using Slim Framework 4.x with PHP-DI 7.
- Codebase must follow PSR-12 coding standards and best practices.
- A code linter (e.g., PHP_CodeSniffer or PHP-CS-Fixer) must be configured to enforce PSR-12 compliance.
- All logic must be modular and DI-based for easy testability and future extensibility.
- OpenAPI (Swagger) annotations must fully document all routes and responses.
- A Swagger UI endpoint (e.g., `/openapi.json`) must be available to visually explore the API.
- All core logic should be covered by PHPUnit tests.
- No UI should be implemented as part of this project — frontend will be handled separately.
- No external services, APIs, or online dependencies may be used at runtime — the system must be fully functional in offline environments.
- Prompt content must be age-appropriate and safe for all listed groups. (see stretch goals.)

## Constraints

- PHP version must be compatible with Slim 4 and PHP-DI 7.
- Seed data must remain in version-controlled JSON files. (at this time)
- No external database should be required — all data is file-based. (at this time)
- A LICENSE file containing the MIT license must be included in the root of the repository.


## Stretch Goals

- **Seed Data Randomization Improvements:**
  - Split `characters` and `settings` into adjective + noun combinations to allow more varied and flexible generation.
  - Decompose `events` into distinct verbs, triggers, or effects to increase permutation variety.
  - Break down `objects` into quality + item (e.g., "glowing stone" → `adjective: glowing`, `noun: stone`) for enhanced recombination and theme filtering.

- **Seed Data Management:**
  - Support for user-submitted seed data via `POST` (with appropriate token-based or similar authentication).
  - Full CRUD support for seed data (Create, Read, Update, Delete).
  - Endpoint(s) to list and manage seed entries securely.
  - All user-submitted content must include age group metadata and be validated for age-appropriateness using heuristics, moderation rules, or admin approval tools.
  - Seed Data *might* be moved to an external data source like MySQL or MongoDB

- **Prompt Filtering:**
  - Add support for keyword filtering, e.g., `/prompt?keywords=magic,robot`.
  - Allow filtering by theme or subgenre (e.g., “Horror”, “Sci-Fi”) if metadata is present in the seed data.

- **More fine-tuned filtering and listing:**
  - The API must expose additional GET routes (e.g., `/data/{age_group}/{category}`) to list all seed entries for each group and category.
