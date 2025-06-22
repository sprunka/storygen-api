# StoryPrompts Improvement Tasks

This document contains a list of actionable improvement tasks for the StoryPrompts project. Each task is logically ordered and covers both architectural and code-level improvements.

## Documentation and Metadata

1. [x] Update project description in composer.json to match README.md (currently says "Character Builder for Outgunned")
2. [x] Add CONTRIBUTING.md with guidelines for contributing to the project
3. [x] Add CODE_OF_CONDUCT.md to establish community guidelines
4. [x] Create API documentation using Swagger UI to visualize the OpenAPI specification with endpoint at `/openapi.json`
5. [x] Add PHPDoc comments to all classes and methods that are missing them
6. [x] Create a CHANGELOG.md file to track version changes
7. [x] Verify or add the MIT license file in the root of the repository

## Code Quality and Architecture

8. [ ] Implement a proper Model for Prompt to encapsulate the data structure
9. [ ] Extract error handling in PromptController into a separate middleware
10. [ ] Create a ResponseFormatter service to standardize JSON responses
11. [ ] Implement a proper dependency injection container configuration file
12. [ ] Add interfaces for all services to improve testability and maintainability
13. [ ] Refactor PromptGenerator to use strategy pattern for different generation methods
14. [ ] Implement a logging system for API requests and errors
15. [ ] Add request validation middleware using a validation library
16. [x] Set up a code linter for PSR-12 compliance

## Core Functionality Requirements

17. [x] Implement the `count` parameter and default 1-3 randomized entries behavior for prompt generation
18. [x] Implement proper handling of invalid age groups (treat as 'any' age group)
19. [x] Implement additional GET routes for data exploration (e.g., `/data/{age_group}/{category}`)

## Error Handling and Validation

20. [x] Add validation for seed.json structure during application bootstrap (OBE)
21. [ ] Implement more specific exception classes for different error types
22. [ ] Add validation for query parameters in all endpoints
23. [ ] Standardize error response format across all endpoints
24. [ ] Add proper HTTP status codes for different error scenarios
25. [ ] Implement rate limiting to prevent abuse

## Testing and Quality Assurance

26. [x] Add unit tests for SeedLoader class
27. [x] Add unit tests for PromptGenerator class
28. [x] Add integration tests for API endpoints
29. [ ] Implement code coverage reporting
30. [x] Add static analysis tools (PHPStan, ~~Psalm~~)
31. [ ] Set up continuous integration with GitHub Actions or similar
32. [x] Add performance benchmarks for API endpoints

## Performance Optimization

33. [x] Implement caching for frequently requested prompts (Will NOT do)
34. [ ] Optimize SeedLoader::getAllElementsByType to avoid duplicating elements
35. [ ] Add a method to refresh the cache if the seed data file changes
36. [x] Implement pagination for endpoints that return large datasets (Will NOT do)
37. [x] Add compression for API responses
38. [ ] Optimize JSON encoding/decoding with a faster library

## Feature Enhancements

39. [ ] Add support for custom seed data files
40. [ ] Implement a feature to combine multiple age groups
41. [ ] Add an endpoint to get all available age groups and element types
42. [ ] Implement a feature to exclude specific elements from generation
43. [ ] Add support for weighted random selection to favor certain elements
44. [ ] Implement a feature to generate prompts based on themes or genres
45. [ ] Add an endpoint to get statistics about the seed data
46. [ ] Implement a feature to save favorite prompts
47. [ ] Add support for user-generated prompts
48. [ ] Implement a simple web interface for generating prompts

## Security Enhancements

49. [ ] Add input sanitization for all user inputs
50. [ ] Implement CORS headers for API endpoints
51. [ ] Add security headers to prevent common web vulnerabilities
52. [ ] Implement API key authentication for sensitive endpoints
53. [ ] Add rate limiting per IP address
54. [ ] Implement request logging for security auditing
55. [ ] Add a security policy document
