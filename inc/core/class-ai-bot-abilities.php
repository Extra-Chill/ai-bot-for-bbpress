<?php

namespace AiBot\Core;

use AiBot\Context\Database_Agent;
use AiBot\Tools\Local_Search_Tool;

class Ai_Bot_Abilities {

	private Database_Agent $database_agent;

	public function __construct( Database_Agent $database_agent ) {
		$this->database_agent = $database_agent;
	}

	public function init() {
		add_action( 'wp_abilities_api_categories_init', array( $this, 'register_categories' ) );
		add_action( 'wp_abilities_api_init', array( $this, 'register_abilities' ) );
	}

	public function register_categories() {
		if ( ! function_exists( 'wp_register_ability_category' ) ) {
			return;
		}

		wp_register_ability_category(
			'ai-bot-search',
			array(
				'label'       => __( 'AI Bot Search', 'ai-bot-for-bbpress' ),
				'description' => __( 'Search abilities used by AI Bot for bbPress.', 'ai-bot-for-bbpress' ),
			)
		);
	}

	public function register_abilities() {
		if ( ! function_exists( 'wp_register_ability' ) ) {
			return;
		}

		wp_register_ability(
			'ai-bot-for-bbpress/local-search',
			array(
				'label'       => __( 'Local Search', 'ai-bot-for-bbpress' ),
				'description' => __( 'Search local WordPress/bbPress content for relevant information.', 'ai-bot-for-bbpress' ),
				'category'    => 'ai-bot-search',
				'input_schema'  => array(
					'type'                 => 'object',
					'properties'           => array(
						'query'           => array(
							'type'        => 'string',
							'description' => __( 'Search query or keywords to find relevant content.', 'ai-bot-for-bbpress' ),
							'minLength'   => 1,
						),
						'limit'           => array(
							'type'        => 'integer',
							'description' => __( 'Maximum number of results to return.', 'ai-bot-for-bbpress' ),
							'minimum'     => 1,
						),
						'exclude_post_id' => array(
							'type'        => 'integer',
							'description' => __( 'Post ID to exclude from results.', 'ai-bot-for-bbpress' ),
							'minimum'     => 1,
						),
						'topic_id'        => array(
							'type'        => 'integer',
							'description' => __( 'Topic ID to scope exclusions.', 'ai-bot-for-bbpress' ),
							'minimum'     => 1,
						),
					),
					'required'             => array( 'query' ),
					'additionalProperties' => false,
				),
				'output_schema' => array(
					'type'                 => 'object',
					'properties'           => array(
						'query'         => array( 'type' => 'string' ),
						'results_count' => array( 'type' => 'integer' ),
						'results'       => array( 'type' => 'array' ),
					),
					'required'             => array( 'query', 'results_count', 'results' ),
					'additionalProperties' => false,
				),
				'execute_callback' => array( $this, 'execute_local_search' ),
				'permission_callback' => '__return_true',
				'meta' => array(
					'show_in_rest' => false,
					'annotations'  => array(
						'readonly'   => true,
						'destructive' => false,
						'idempotent' => true,
					),
				),
			)
		);

	}

	public function execute_local_search( $input ) {
		$tool = new Local_Search_Tool( $this->database_agent );
		return $tool->search( is_array( $input ) ? $input : array() );
	}
}
