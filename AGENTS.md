# Agent Instructions (ai-bot-for-bbpress)

## Architectural Principles

- KISS (KEEP IT SIMPLE, STUPID): always favor the most direct, centralized solution
- The single responsibility principle is paramount. Each file must be responsible for a single responsibility with no exceptions.
- Human-readable code is required, reducing the need for inline code comments
- All data must have a single source of truth, streamlining data contracts throughout the codebase
- Use the REST API over admin-ajax.php
- Use vanilla javascript over jQuery
- Use WordPress filters and actions for extensibility, when beneficial
- Use Object-Oriented Programming practices to reduce code duplication and complexity
- Use the backend as the single source of truth
- Standardize data contracts and access patterns
- All new projects start at version 0.1.0 and use semantic versioning

## FORBIDDEN PRACTICES
- Do NOT leave inline comments about removed or modified code
- Do NOT use !important in .css files (except editor styles in WordPress)
- Do NOT change AI models in the code
- Do NOT use any of the following fallback types

## FORBIDDEN FALLBACK TYPES

- Placeholder fallbacks for undefined parameters
- Legacy fallbacks for removed functionality
- Fallbacks that prevent code failure or hide broken functionality
- Fallbacks that provide dual support for multiple data contracts
- BACKWARD COMPATIBILITY IS BLOAT AND SHOULD BE ELIMINATED

## Planning Standards (Plan Mode)
- Create specific and refined plans that outline all changes EXACTLY as they will be implemented
- Plans must explicitly identify which files and functions to modify, create, or delete
- All code review should be completed BEFORE presenting plans

## Documentation Standards
- docs/CHANGELOG.md is the single source of truth for the changelog
- Use concise inline docblocks at the top of files to explain technicalities
- Remove redundant or outdated inline comments
- Actively remove references to deleted functionality
- DO NOT CHANGE VERSION NUMBERS UNLESS EXPLICITLY INSTRUCTED TO DO SO

## Standardized Build Process

- Create a `build.sh` shell command that creates an optimized .zip for production use with all production dependencies bundled
- Production build structure:
  - `/build/[root-directory-name].zip` - Production ZIP file only (no other directories or files inside of /build)
- File exclusions: Exclude development files (vendor/, node_modules/, .git/, docs/, build files, composer.lock, package-lock.json, .DS_Store, .claude/, README.MD, .buildignore, build.sh, AGENTS.md)
- Use `composer install --no-dev` for production dependencies only
- Composer scripts must use `vendor/bin/` prefix for tool paths

## User Info

- Name: Chris Huber
- Dev website: https://chubes.net
- GitHub: https://github.com/chubes4
- Founder & Editor: https://extrachill.com - WordPress multisite network
- Github Organization: https://github.com/Extra-Chill
- Creator: https://saraichinwag.com
- WordPress.org username: extrachill
- Uses Cloudways for hosting
- Local WP dev site: testing-grounds.local (supports multisite & wp-cli): /Users/chubes/Developer/LocalWP/testing-grounds/app

## Style Guide
- Implement clean CSS design systems using root.css as the single source of truth for all css variables
- root.css is available throughout the system. It contains all variable definitions and reusable components
- Other modular stylesheets provide nuanced functionality for specific features
- There should be zero inline css ever

## CSS Units

- Use 'px':
    - Border widths
    - Small fixed dimensions
    - When precision is needed
- Use 'rem':
    - Font sizes
    - Spacing (margin, padding)
    - Layout dimensions (width, height)

## Commands
- Install dependencies: `composer install`
- Validate PHP syntax: `php -l ai-bot-for-bbpress.php`
- Search for legacy patterns: `rg -n "ai_http_execute_tool\(|apply_filters\(\s*'ai_request'|ai_render_component" -S . --glob "!AGENTS.md"` (should return no matches)

## Code Style
- PHP: follow WordPress coding standards (spaces inside parentheses, `array()` ok; no short tags).
- Namespaces use `AiBot\\...`; classes use `StudlyCaps` with underscores (e.g. `Generate_Bot_Response`).
- Prefer small single-responsibility classes in `inc/{core,context,tools,admin}`.
- Use WordPress APIs (`add_action`, `add_filter`, `wp_remote_*`, `WP_Error`) and `apply_filters()` for extensibility.
- Sanitize on input (`sanitize_text_field`, `absint`, `esc_url_raw`, `wp_kses_post`) and escape on output (`esc_html`, `esc_attr`, `esc_textarea`).
- Error handling: return `WP_Error` (or `['error'=>...,'results'=>[]]` for tool results); never silently swallow failures.
- Options are the source of truth; avoid hardcoded fallbacks unless explicitly required.
- JS: vanilla only (no jQuery); module-free scripts; prefer `addEventListener` + `querySelector`.

## Repo Rule Files
- No `.cursor/rules/`, `.cursorrules`, or `.github/copilot-instructions.md` currently in this repo.
