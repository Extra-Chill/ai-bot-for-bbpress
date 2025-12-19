<?php

namespace AiBot\Core;

use AiBot\Context\Content_Interaction_Service;
use AiBot\Core\System_Prompt_Builder;
use AiBot\Core\AiBot_Service_Container;
use WordPress\AI_Client\AI_Client;
use WordPress\AI_Client\Builders\Helpers\Ability_Function_Resolver;
use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Messages\DTO\MessagePart;
use WordPress\AiClient\Messages\DTO\ModelMessage;
use WordPress\AiClient\Messages\DTO\UserMessage;

/**
 * Generate Bot Response Class
 * 
 * Orchestrates AI response generation by coordinating context retrieval, 
 * prompt construction, and API communication.
 */
class Generate_Bot_Response {

    /**
     * @var Content_Interaction_Service
     */
    private $content_interaction_service;

    /**
     * @var System_Prompt_Builder
     */
    private $system_prompt_builder;

    /**
     * @var AiBot_Service_Container
     */
    private $container;

    /**
     * Constructor
     *
     * @param Content_Interaction_Service $content_interaction_service The content interaction service instance.
     * @param System_Prompt_Builder $system_prompt_builder The system prompt builder instance.
     * @param AiBot_Service_Container $container The service container instance.
     */
    public function __construct(
        Content_Interaction_Service $content_interaction_service,
        System_Prompt_Builder $system_prompt_builder,
        AiBot_Service_Container $container
    ) {
        $this->content_interaction_service = $content_interaction_service;
        $this->system_prompt_builder = $system_prompt_builder;
        $this->container = $container;
    }

    /**
     * Cron function to generate and post AI response
     */
    public function generate_and_post_ai_response_cron( $post_id, $topic_id, $forum_id, $anonymous_data, $reply_author ) {

        $bot_user_id = get_option( 'ai_bot_user_id' );

        if ( ! $bot_user_id ) {
            return;
        }

        $bot_user_data = get_userdata( $bot_user_id );

        if ( ! $bot_user_data ) {
            return;
        }

        $bot_username = $bot_user_data->user_login;

        $triggering_username_slug = 'the user';
        if ( $reply_author != 0 ) {
            $triggering_user_data = get_userdata( $reply_author );
            if ( $triggering_user_data ) {
                $triggering_username_slug = $triggering_user_data->user_nicename;
            }
        }

        $post_content = ($reply_author == 0) ? bbp_get_topic_content( $post_id ) : bbp_get_reply_content( $post_id );
        $response_content = $this->generate_ai_response( $bot_username, $post_content, $topic_id, $forum_id, $post_id, $triggering_username_slug );

        if ( is_wp_error( $response_content ) ) {
             $response_content = __( 'I received your message but I\'m having some technical difficulties right now. Please try again in a few minutes!', 'ai-bot-for-bbpress' );
        }

        $bot_instance = $this->container->get('bot.main');
        $bot_instance->post_bot_reply( $topic_id, $response_content );
    }

    /**
     * Create standard API error
     */
    private function create_api_error() {
        return new \WP_Error( 'api_error', __( 'Sorry, I\'m having trouble generating a response right now. Please try again later.', 'ai-bot-for-bbpress' ) );
    }

    /**
     * Generate AI response using wp-ai-client and Abilities.
     */
    private function generate_ai_response( $bot_username, $post_content, $topic_id, $forum_id, $post_id, $triggering_username_slug ) {
        $system_prompt = $this->system_prompt_builder->build_system_prompt( $bot_username );

        $current_context = $this->content_interaction_service->get_current_interaction_context( $post_id, $post_content, $topic_id, $forum_id );

        $conversation_messages = $this->content_interaction_service->get_conversation_messages( $post_id, $post_content, $topic_id, $forum_id );

        $response_instructions = $this->system_prompt_builder->build_response_instructions( $bot_username, $triggering_username_slug );

        $history_messages = $this->convert_conversation_history_to_messages( $conversation_messages );

        $user_prompt = $current_context . $response_instructions;

        $local_search_enabled = (bool) get_option( 'ai_bot_enable_local_search', 1 );

        $abilities_context = array(
            'post_id'                  => $post_id,
            'topic_id'                 => $topic_id,
            'forum_id'                 => $forum_id,
            'bot_username'             => $bot_username,
            'triggering_username_slug' => $triggering_username_slug,
            'local_search_enabled'     => $local_search_enabled,
        );

        $default_abilities = $local_search_enabled ? array( 'ai-bot-for-bbpress/local-search' ) : array();
        $abilities = apply_filters( 'ai_bot_enabled_abilities', $default_abilities, $abilities_context );

        if ( ! is_array( $abilities ) ) {
            return new \WP_Error( 'ai_bot_invalid_enabled_abilities', __( 'The ai_bot_enabled_abilities filter must return an array of ability names.', 'ai-bot-for-bbpress' ) );
        }

        foreach ( $abilities as $ability ) {
            if ( ! is_string( $ability ) || $ability === '' ) {
                return new \WP_Error( 'ai_bot_invalid_enabled_abilities', __( 'The ai_bot_enabled_abilities filter must return an array of non-empty strings.', 'ai-bot-for-bbpress' ) );
            }
        }

        $abilities = array_values( $abilities );

        $builder = AI_Client::prompt_with_wp_error( null )
            ->using_system_instruction( $system_prompt )
            ->with_history( ...$history_messages )
            ->with_text( $user_prompt )
            ->using_temperature( (float) get_option( 'ai_bot_temperature', 0.5 ) )
            ->as_output_mime_type( 'text/html' );

        if ( ! empty( $abilities ) ) {
            $builder->using_abilities( ...$abilities );
        }

        $max_iterations = 5;
        $result = $builder->generate_text_result();

        if ( is_wp_error( $result ) ) {
            return $result;
        }

        $iteration = 0;
        while ( $iteration < $max_iterations ) {
            $message = $result->toMessage();

            if ( ! Ability_Function_Resolver::has_ability_calls( $message ) ) {
                break;
            }

            $abilities_response_message = Ability_Function_Resolver::execute_abilities( $message );

            $history_messages[] = $message;
            $history_messages[] = $abilities_response_message;

            $builder = AI_Client::prompt_with_wp_error( null )
                ->using_system_instruction( $system_prompt )
                ->with_history( ...$history_messages )
                ->with_text( $user_prompt )
                ->using_temperature( (float) get_option( 'ai_bot_temperature', 0.5 ) )
                ->as_output_mime_type( 'text/html' );

            if ( ! empty( $abilities ) ) {
                $builder->using_abilities( ...$abilities );
            }

            $result = $builder->generate_text_result();
            if ( is_wp_error( $result ) ) {
                return $result;
            }

            $iteration++;
        }

        return $result->toText();
    }

    private function convert_conversation_history_to_messages( array $conversation_messages ): array {
        $messages = array();

        foreach ( $conversation_messages as $entry ) {
            if ( ! is_array( $entry ) ) {
                continue;
            }

            $role = $entry['role'] ?? '';
            $content = $entry['content'] ?? '';

            if ( ! is_string( $content ) || $content === '' ) {
                continue;
            }

            if ( $role === 'assistant' ) {
                $messages[] = new ModelMessage( array( new MessagePart( $content ) ) );
                continue;
            }

            $messages[] = new UserMessage( array( new MessagePart( $content ) ) );
        }

        if ( ! empty( $messages ) ) {
            $last_message = $messages[ count( $messages ) - 1 ];
            if ( $last_message instanceof UserMessage ) {
                array_pop( $messages );
            }
        }

        return $messages;
    }

}