# Changelog

All notable changes to the Story Prompts API will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial API implementation with endpoints for generating story prompts
- Support for age group filtering (kids, teens, adults)
- OpenAPI documentation at `/openapi.json`
- Unit and integration tests

### Changed
- Restructured project to follow a more organized architecture:
  - Changed namespace from `App` to `StoryGen`
  - Reorganized directory structure with dedicated config directory
  - Separated configuration into dedicated files
  - Created AbstractController base class
  - Moved Swagger documentation to dedicated Docs directory
- Downgraded Swagger-PHP from v5.1.3 to v3.3 for better compatibility
- Updated tests to use new namespace and structure

### Deprecated

### Removed

### Fixed

### Security

## [0.0.1] - 2023-10-01

### Added
- Initial project setup
- Basic API structure
- Seed data for story prompts

[Unreleased]: https://github.com/sprunka/story-prompt-generator/compare/v0.0.1...HEAD
[0.0.1]: https://github.com/sprunka/story-prompt-generator/releases/tag/v0.0.1
