<?php

namespace AiBot\Tools;

use AiBot\Context\Database_Agent;

class Local_Search_Tool {

	private Database_Agent $database_agent;

	public function __construct( Database_Agent $database_agent ) {
		$this->database_agent = $database_agent;
	}

	public function search( array $input ) {
		$query = isset( $input['query'] ) ? sanitize_text_field( $input['query'] ) : '';
		$exclude_post_id = isset( $input['exclude_post_id'] ) ? absint( $input['exclude_post_id'] ) : 0;
		$topic_id = isset( $input['topic_id'] ) ? absint( $input['topic_id'] ) : 0;

		$limit = isset( $input['limit'] ) ? absint( $input['limit'] ) : 0;
		$max_limit = absint( get_option( 'ai_bot_local_search_limit', 3 ) );
		$effective_limit = max( 1, min( $max_limit, max( 1, $limit ) ) );

		if ( '' === $query ) {
			return new \WP_Error( 'ai_bot_invalid_search_query', __( 'Search query is required.', 'ai-bot-for-bbpress' ) );
		}

		$search_results = $this->database_agent->search_local_content_by_keywords(
			$query,
			$effective_limit,
			$exclude_post_id ? $exclude_post_id : null,
			$topic_id
		);

		$formatted_results = array();
		foreach ( $search_results as $post ) {
			$formatted_result = array(
				'id'      => $post->ID,
				'title'   => get_the_title( $post ),
				'type'    => $post->post_type,
				'date'    => get_the_date( '', $post ),
				'url'     => get_permalink( $post ),
				'content' => $this->get_clean_content( $post ),
			);

			if ( function_exists( 'bbp_get_topic_post_type' ) && function_exists( 'bbp_get_reply_post_type' ) ) {
				$topic_post_type = bbp_get_topic_post_type();
				$reply_post_type = bbp_get_reply_post_type();

				if ( $post->post_type === $topic_post_type ) {
					$forum_id = bbp_get_topic_forum_id( $post->ID );
					if ( $forum_id ) {
						$formatted_result['forum'] = bbp_get_forum_title( $forum_id );
					}
				} elseif ( $post->post_type === $reply_post_type ) {
					$forum_id = bbp_get_reply_forum_id( $post->ID );
					if ( $forum_id ) {
						$formatted_result['forum'] = bbp_get_forum_title( $forum_id );
					}
				}
			}

			$formatted_results[] = $formatted_result;
		}

		return array(
			'query'         => $query,
			'results_count' => count( $formatted_results ),
			'results'       => $formatted_results,
		);
	}

	private function get_clean_content( $post ) {
		$content = get_post_field( 'post_content', $post );
		return trim( html_entity_decode( wp_strip_all_tags( $content ), ENT_QUOTES, 'UTF-8' ) );
	}
}
