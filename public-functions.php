<?php

use PhHelpers\Plugin;

/**
 * @return \PhHelpers\Plugin
 */
function ph_helpers_get_plugin() {
	return Plugin::instance();
}

function ph_helpers_is_plugin_active( $plugin ) {
	return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );
}

/**
 * Load all related posts of a given post by its field_id
 */
function ph_helpers_load_content_relations( $post, $field ) {

	$ids = ph_helpers_load_content_relations_ids( $post, $field );

	$posts = [];
	foreach ( $ids as $id ) {
		$posts[] = get_post( $id );
	}

	return $posts;
}

/**
 * Returns an array with ids of the related posts
 *
 * @param $post
 *
 * @return array
 */
function ph_helpers_load_content_relations_ids( $post, $field ) {
	if ( ph_helpers_is_plugin_active( 'content-relations/ph-content-relations.php' )
	     && get_option( 'ph-helpers-use-content-relations', false ) == true ) {

		$store     = \content_relations_get_store( $post->ID );
		$relations = $store->get_relations_by_type( $field, $source_only = true );
		$ids       = [];

		foreach ( $relations as $relation ) {
			$ids[] = $relation->target_id;
		}

		return $ids;
	} else {
		$ids = get_post_meta( $post->ID, $field, false );
		if ( $ids ) {
			if ( ! is_array( $ids ) ) {
				$ids = explode( $ids );
			}

			$ids = array_map( 'intval', $ids );

			return $ids;
		}
	}
}

/**
 * Get first paragraph from html string
 *
 * @param $html
 *
 * @return string
 */
function ph_helpers_get_first_paragraph( $html ) {
	if ( empty( $html ) || ! is_string( $html ) ) {
		return "";
	}

	$paragraph = substr( $html, 0, 200 ) . "...";

	return html_entity_decode( strip_tags( $paragraph ) );
}
