<?php
/**
 * Plugin Name: AI Bot for bbPress
 * Plugin URI:  https://wordpress.org/plugins/ai-bot-for-bbpress/
 * Description: Universal AI bot for bbPress forums with multi-provider support (OpenAI, Anthropic, Gemini, Grok, OpenRouter).
 * Version:     2.0.0
 * Author:      Chubes
 * Author URI:  https://chubes.net
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires PHP: 7.4
 * Requires at least: 6.9
 * Requires Plugins: bbpress
 * Text Domain: ai-bot-for-bbpress
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AI_BOT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

define( 'AI_BOT_PLUGIN_FILE', __FILE__ );

$autoload_path = AI_BOT_PLUGIN_PATH . 'vendor/autoload.php';
if ( file_exists( $autoload_path ) ) {
	require_once $autoload_path;
}

require_once AI_BOT_PLUGIN_PATH . 'inc/core/class-ai-bot-service-container.php';

require_once AI_BOT_PLUGIN_PATH . 'inc/core/class-generate-bot-response.php';
require_once AI_BOT_PLUGIN_PATH . 'inc/core/class-system-prompt-builder.php';
require_once AI_BOT_PLUGIN_PATH . 'inc/core/class-bot-trigger-service.php';
require_once AI_BOT_PLUGIN_PATH . 'inc/context/class-database-agent.php';
require_once AI_BOT_PLUGIN_PATH . 'inc/context/class-forum-structure-provider.php';
require_once AI_BOT_PLUGIN_PATH . 'inc/context/class-content-interaction-service.php';
require_once AI_BOT_PLUGIN_PATH . 'inc/tools/class-local-search-tool.php';
require_once AI_BOT_PLUGIN_PATH . 'inc/core/class-ai-bot-abilities.php';
require_once AI_BOT_PLUGIN_PATH . 'inc/core/class-ai-bot.php';

require_once AI_BOT_PLUGIN_PATH . 'inc/admin/admin-central.php';

use AiBot\Core\AiBot_Service_Container;
use AiBot\Core\AiBot;
use AiBot\Core\Ai_Bot_Abilities;
use AiBot\Context\Database_Agent;
use AiBot\Context\Content_Interaction_Service;
use AiBot\Context\Forum_Structure_Provider;
use AiBot\Core\Generate_Bot_Response;
use AiBot\Core\System_Prompt_Builder;
use AiBot\Core\Bot_Trigger_Service;
use WordPress\AI_Client\AI_Client;


add_action( 'init', array( AI_Client::class, 'init' ) );

add_action( 'admin_init', static function() {
	$migrated = get_option( 'ai_bot_migrated_to_wp_ai_client' );
	if ( $migrated ) {
		return;
	}

	delete_option( 'ai_bot_selected_provider' );
	delete_option( 'ai_bot_selected_model' );
	delete_option( 'ai_bot_remote_endpoint_url' );
	delete_option( 'ai_bot_remote_search_limit' );

	update_option( 'ai_bot_migrated_to_wp_ai_client', 1, false );
} );


$container = new AiBot_Service_Container();



$container->register( 'context.database_agent', function( $c ) {
    return new Database_Agent();
} );


// Register the forum structure provider
$container->register( 'context.forum_structure_provider', function( $c ) {
    return new Forum_Structure_Provider();
} );

// Register the system prompt builder
$container->register( 'core.system_prompt_builder', function( $c ) {
    return new System_Prompt_Builder(
        $c->get( 'context.forum_structure_provider' )
    );
} );

// Register the bot trigger service
$container->register( 'core.bot_trigger_service', function( $c ) {
    return new Bot_Trigger_Service();
} );

// Register the content interaction service
$container->register( 'context.interaction_service', function( $c ) {
    return new Content_Interaction_Service(
        $c->get( 'context.database_agent' )
    );
} );


// Register the response generation service
$container->register( 'core.generate_bot_response', function( $c ) {
    return new Generate_Bot_Response(
        $c->get( 'context.interaction_service' ),
        $c->get( 'core.system_prompt_builder' ),
        $c // Pass the container itself
    );
} );

// Register abilities.
$container->register( 'core.abilities', function( $c ) {
	return new Ai_Bot_Abilities( $c->get( 'context.database_agent' ) );
} );

// Register the main bot class
$container->register( 'bot.main', function( $c ) {
    return new AiBot(
        $c->get( 'core.bot_trigger_service' ),
        $c->get( 'core.generate_bot_response' ),
        $c->get( 'context.interaction_service' ),
        $c->get( 'context.database_agent' )
    );
} );


$abilities = $container->get( 'core.abilities' );
$abilities->init();

// Instantiate and Initialize the Bot via the Container
$ai_bot_instance = $container->get( 'bot.main' );
$ai_bot_instance->init();



global $ai_bot_container;
$ai_bot_container = $container;

