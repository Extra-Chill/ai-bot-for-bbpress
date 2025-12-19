# Migration Notes: AI Bot for bbPress -> wp-ai-client + Abilities (Agentic Tools)

This file documents the completed migration to WordPress core Abilities + `wordpress/wp-ai-client`.

## Summary

- WordPress minimum version is **6.9** (Abilities API in core).
- Provider credentials are configured in WordPress core under **Settings > AI Credentials**.
- The plugin registers Abilities for local search.
- Response generation uses an agent loop that continues until there are no more ability calls.
- Bot output is **HTML only**.

## What Changed

### Removed

- Legacy AI client and related references (including old tool schema and execution helpers).
- Plugin-managed provider/model selection options:
  - `ai_bot_selected_provider`
  - `ai_bot_selected_model`

### Added

- Abilities registration:
  - `ai-bot-for-bbpress/local-search`

- Search tools now expose `search( array $input )` for ability execution.

- Response generation now uses `WordPress\AI_Client\AI_Client` and ability function resolution.

## Files (Primary)

- `ai-bot-for-bbpress.php`
- `inc/core/class-ai-bot-abilities.php`
- `inc/core/class-generate-bot-response.php`
- `inc/tools/class-local-search-tool.php`
- `inc/admin/register-settings.php`

## Verification

- Bot responses are HTML (no Markdown fences).
- Provider credentials are managed in Settings > AI Credentials.
- Local search is exposed via Abilities.
