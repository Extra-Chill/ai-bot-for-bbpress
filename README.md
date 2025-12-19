# AI Bot for bbPress

A WordPress plugin that integrates an AI-powered bot into bbPress forums.

This plugin uses WordPress core's AI stack (Abilities + `wordpress/wp-ai-client`) and supports agentic tool calling for search.

## Features

- **AI Provider Credentials via WP Core**: Configure provider keys in Settings → AI Credentials
- **Agentic Search**: The model chooses when to call search abilities
- **Smart Bot Triggers**: Responds to @mentions, keywords, and forum restrictions
- **Forum Access Control**: Restrict bot to specific forums with hierarchical selection
- **bbPress Integration**: Works with bbPress hooks and ecosystem plugins

## Installation

1. **Requirements**:
   - WordPress 6.9+
   - PHP 7.4+
   - bbPress plugin installed and activated

2. **Install Plugin**:
   ```bash
   # Via WordPress admin
   Plugins > Add New > Upload Plugin > Select ai-bot-for-bbpress.zip

   # Via WordPress CLI
   wp plugin install ai-bot-for-bbpress.zip --activate
   ```

3. **Configure AI Credentials**:
   - Go to Settings > AI Credentials
   - Add credentials for your preferred provider

4. **Configure Bot**:
   - Go to Settings > Forum AI Bot
   - Set the bot user account and behavior settings

## Configuration

### Basic Setup

```php
update_option( 'ai_bot_user_id', 2 );
update_option( 'ai_bot_trigger_keywords', 'help,question,support' );
```

### Forum Access Control

```php
update_option( 'ai_bot_forum_restriction', 'selected' );
update_option( 'ai_bot_allowed_forums', array( 12, 15, 18 ) );
```

### Context Settings

```php
update_option( 'ai_bot_local_search_limit', 5 );
```

### System Prompts

```php
update_option(
	'ai_bot_system_prompt',
	'You are a helpful WordPress expert specializing in bbPress forums. Provide clear, actionable advice.'
);

update_option(
	'ai_bot_custom_prompt',
	'Always answer in HTML.'
);
```

### Temperature Control

```php
update_option( 'ai_bot_temperature', 0.7 );
```

## Development

### Architecture

```php
AiBot\\Core\\AiBot
AiBot\\Core\\Bot_Trigger_Service
AiBot\\Core\\Generate_Bot_Response
AiBot\\Core\\System_Prompt_Builder
AiBot\\Core\\Ai_Bot_Abilities

AiBot\\Tools\\Local_Search_Tool

AiBot\\Context\\Content_Interaction_Service
AiBot\\Context\\Database_Agent
```

### Extending

- Custom behavior is extended via plugin-specific filters like `ai_bot_should_respond`, `ai_bot_system_prompt`, and `ai_bot_response_content`.
- Tool calling is implemented via Abilities; register additional abilities if you want the model to call more tools.

## Requirements

- **WordPress**: 6.9+
- **PHP**: 7.4+
- **bbPress**: Latest version

## License

GPLv2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## Support

- **Issues**: https://github.com/chubes4/ai-bot-for-bbpress/issues
- **WordPress.org**: https://wordpress.org/plugins/ai-bot-for-bbpress/
