# Story Prompts API - Improvement Plan

## Introduction

This document outlines a comprehensive improvement plan for the Story Prompts API project. Based on the requirements specified in `requirements.md` and an analysis of the current codebase, this plan identifies key areas for enhancement and provides a roadmap for implementation. The plan is organized by themes to facilitate focused development efforts.

## Core Functionality Enhancements

### Prompt Generation Improvements

1. **Implement Variable Prompt Size**
   - Rationale: The requirements specify that prompts should return 1-3 entries per category (randomized by default).
   - Current implementation returns exactly one entry per category.
   - Modify `PromptGenerator::generatePrompt()` to support multiple entries per category.
   - Add a `count` parameter to control the number of entries per category.
   - Default behavior should be randomized between 1-3 entries per category.

2. **Age Group Validation and Handling**
   - Rationale: Requirements specify that invalid age groups must behave as 'any' age group.
   - Implement proper validation to ensure invalid age groups default to 'any' behavior.
   - Ensure content remains age-appropriate for all listed groups.
   - Add metadata to seed entries to facilitate filtering.

3. **Additional GET Routes for Data Exploration**
   - Rationale: Requirements mention additional GET routes to list all values by age group and category.
   - Implement endpoints like `/data/{age_group}/{category}` to list all available elements.
   - Add documentation for these endpoints using OpenAPI annotations.

## Data Management

1. **Seed Data Structure Validation**
   - Rationale: The seed data is critical to the application and should be validated on load.
   - Implement schema validation for the seed.json file.
   - Add error handling for malformed or missing data.

2. **Seed Data Expansion**
   - Rationale: The seed data should be easy to update or expand.
   - Create a clear process for adding new entries to the seed data.
   - Document the seed data format and requirements.

3. **Stretch Goal: Seed Data Management API**
   - Rationale: Support for user-submitted seed data via POST is listed as a stretch goal.
   - Design a secure API for managing seed data with authentication.
   - Implement CRUD operations for seed entries.

## Architecture and Code Quality

1. **PSR-12 Compliance**
   - Rationale: Requirements mandate PSR-12 coding standards and a code linter.
   - Set up a code linter (PHP_CodeSniffer or PHP-CS-Fixer) for PSR-12 compliance.
   - Ensure all code follows PSR-12 standards.

2. **Implement Proper Model for Prompt**
   - Rationale: Current implementation lacks a dedicated model for Prompt data.
   - Create a `Prompt` model class to encapsulate the data structure.
   - Use the model in the PromptGenerator and PromptController.

3. **Standardize Response Format**
   - Rationale: API responses should follow a consistent format.
   - Create a ResponseFormatter service to standardize JSON responses.
   - Ensure all endpoints return responses in the same format.

4. **Improve Error Handling**
   - Rationale: Current error handling is basic and could be more robust.
   - Extract error handling in PromptController into middleware.
   - Implement more specific exception classes for different error types.
   - Standardize error response format across all endpoints.

5. **Enhance Dependency Injection**
   - Rationale: The current DI setup is functional but could be improved.
   - Create a proper dependency injection container configuration file.
   - Add interfaces for all services to improve testability and maintainability.

## Documentation and Metadata

1. **Update Project Description**
   - Rationale: The composer.json description doesn't match the project's purpose.
   - Update description to accurately reflect the Story Prompts API.

2. **Complete OpenAPI Documentation**
   - Rationale: Requirements specify that OpenAPI annotations must fully document all routes and responses, with a Swagger UI endpoint at `/openapi.json`.
   - Ensure all endpoints are properly documented with OpenAPI annotations.
   - Create a specific endpoint at `/openapi.json` for Swagger UI visualization.
   - Add examples and detailed descriptions for all parameters and responses.

3. **Add Contributing Guidelines**
   - Rationale: To facilitate community contributions, guidelines should be provided.
   - Create CONTRIBUTING.md with clear instructions for contributing.
   - Add CODE_OF_CONDUCT.md to establish community guidelines.

4. **License Requirements**
   - Rationale: Requirements specify that a MIT license file must be included.
   - Verify or add the MIT license file in the root of the repository.

## Testing and Quality Assurance

1. **Expand Test Coverage**
   - Rationale: Requirements specify that all core logic should be covered by PHPUnit tests.
   - Add unit tests for SeedLoader and PromptGenerator classes.
   - Add integration tests for API endpoints.
   - Implement code coverage reporting.

2. **Add Static Analysis**
   - Rationale: Static analysis can help identify potential issues before they cause problems.
   - Set up PHPStan or Psalm for static code analysis.
   - Configure CI/CD to run static analysis on code changes.

## Performance Optimization

1. **Implement Caching**
   - Rationale: Caching can improve performance for frequently requested prompts.
   - Add caching for seed data to avoid repeated file reads.
   - Consider caching generated prompts for common parameters.

2. **Optimize Data Loading**
   - Rationale: The current implementation loads all seed data even when only a portion is needed.
   - Optimize SeedLoader to load only the required data.
   - Improve memory usage for large seed data files.

## Security Enhancements

1. **Input Validation and Sanitization**
   - Rationale: All user inputs should be validated and sanitized to prevent security issues.
   - Add validation for query parameters in all endpoints.
   - Implement request validation middleware.

2. **API Protection**
   - Rationale: The API should be protected from abuse.
   - Implement rate limiting to prevent excessive requests.
   - Add security headers to prevent common web vulnerabilities.

## Stretch Goals Implementation

1. **Keyword Filtering**
   - Rationale: Keyword filtering is listed as a stretch goal.
   - Design and implement a filtering system based on keywords.
   - Add a `keywords` parameter to the prompt generation endpoint.

2. **Theme/Subgenre Filtering**
   - Rationale: Filtering by theme or subgenre is listed as a stretch goal.
   - Add metadata to seed entries for themes and subgenres.
   - Implement filtering based on this metadata.

## Conclusion

This improvement plan provides a comprehensive roadmap for enhancing the Story Prompts API. By addressing these areas, the project will better meet the requirements specified in the requirements document and provide a more robust, maintainable, and feature-rich API for generating creative writing prompts.

Implementation should be prioritized based on the core functionality requirements first, followed by architecture improvements, testing, and finally the stretch goals. Regular reviews of progress against this plan will help ensure that the project stays on track and meets all requirements.
