# Changelog

All notable changes to this project will be documented in this file.

## 2.0.0
- BREAKING: Removed remote search/helper endpoint feature
- New: Setting to disable built-in local search ability
- New: `ai_bot_enabled_abilities` filter for custom abilities
- ARCHITECTURE: Complete migration from ai-http-client library to wordpress/wp-ai-client
- ARCHITECTURE: Provider configuration now uses WordPress core AI Credentials instead of plugin settings
- REMOVED: Provider and model selection from plugin admin (now handled by WP core)
- UPDATED: Autoloading system to use composer vendor/autoload.php
- UPDATED: Bot response generation to use new AI_Client API and Message DTOs
- CLEANUP: Removed lib/ai-http-client/ directory and all custom AI client code

## 1.0.5
- 🚀 REVOLUTIONARY: Multi-Provider AI Support - Choose from 5 AI providers!
- NEW: Support for OpenAI, Anthropic (Claude), Google Gemini, Grok (X.AI), and OpenRouter
- NEW: Professional provider management interface with seamless switching
- NEW: Dynamic model selection - automatically fetch available models from any provider
- NEW: Unified admin interface for all providers with core components
- ENHANCEMENT: Backward compatibility - existing OpenAI configurations work seamlessly
- ENHANCEMENT: Provider-agnostic architecture - switch providers without code changes
- ENHANCEMENT: Real-time model fetching with proper error handling and UX feedback
- ARCHITECTURE: Complete integration of AI HTTP Client library (7,400+ lines of new code)
- ARCHITECTURE: Unified normalizers for consistent cross-provider functionality
- UPGRADE: From single-provider to universal AI bot platform

## 1.0.4
- Fix: Bot replies now properly trigger WordPress hooks (bbp_new_reply) for better integration
- Enhancement: Bot replies now work correctly with notification plugins, points systems, and activity feeds
- Enhancement: Improved compatibility with bbPress ecosystem plugins that expect standard hooks

## 1.0.3
- NEW FEATURE: Forum Access Control - Bot can now be restricted to specific forums only
- NEW: Hierarchical forum selection with proper indentation showing parent/child relationships
- MAJOR: Improved conversation memory - Bot now uses proper OpenAI message structure for better context retention
- ARCHITECTURE: Complete code refactor with centralized System_Prompt_Builder for all AI instructions
- ARCHITECTURE: Eliminated redundant wrapper classes and directories for cleaner, more maintainable code
- ARCHITECTURE: New Bot_Trigger_Service centralizes all trigger logic (mentions, keywords, forum restrictions)
- Enhancement: Forum restriction enforcement - bot will only respond in selected forums when configured
- Enhancement: Bot now posts user-friendly error messages instead of staying silent when API errors occur
- Enhancement: Conversation history now uses proper user/assistant message alternation for OpenAI API
- Enhancement: All prompt construction logic now centralized in single service following single responsibility principle
- Fix: Bot now properly recognizes its own username when mentioned (no longer thinks mentions are about someone else)
- Fix: Added proper output escaping for admin form inputs to meet WordPress security standards
- Code cleanup: Removed redundant code and directories, improved separation of concerns

## 1.0.2
- Address WordPress.org review feedback:
  - Added 'Requires Plugins: bbpress' header to main plugin file.
  - Updated readme.txt with detailed 'External Services' disclosure for OpenAI API.
  - Removed unnecessary PHP closing tags (`?>`) from several files.
  - Commented out `error_log` and `print_r` development debugging statements.
  - Replaced `strip_tags()` with `wp_strip_all_tags()` in database agent.
  - Restored `unset()` for a class property in database agent after filter removal.
- Enhanced bot response context to more clearly identify the user being replied to.

## 1.0.0
- Major Refactor: Renamed plugin to "AI Bot for bbPress" and updated internal naming conventions.
- Feature: Added optional remote context retrieval via BBP Bot Helper companion plugin.
- Feature: Added Local and Remote search limit settings.
- Feature: Added Trigger Keywords setting.
- Feature: Added date/timestamp information to remote context results.
- Fix: Resolved issues with local and remote context not being correctly passed to the AI model.
- Fix: Improved reliability of cron job scheduling and execution logging.
- Update: Added configuration options for temperature and prompts.
- Update: Tested compatibility up to WordPress 6.8.

## 0.1.2
- Updated plugin name and description.
- Added helper plugin for remote context.

## 0.1.1
- Bug fixes and minor improvements.

## 0.1.0
- Initial release.
