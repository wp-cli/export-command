<?php

use WP_CLI\Utils;

function wpcli_export( $args = array() ) {
	$defaults     = array(
		'filters'     => array(),
		'format'      => 'WP_Export_WXR_Formatter',
		'writer'      => 'WP_Export_Returner',
		'writer_args' => null,
	);
	$args         = wp_parse_args( $args, $defaults );
	$export_query = new WP_Export_Query( $args['filters'] );
	$formatter    = new $args['format']( $export_query );
	$writer       = new $args['writer']( $formatter, $args['writer_args'] );
	try {
		return $writer->export();
	} catch ( WP_Export_Exception $e ) {
		return new WP_Error( 'wp-export-error', $e->getMessage() );
	}
}

function wpcli_export_new_style_args_from_old_style_args( $args ) {
	if ( isset( $args['content'] ) ) {
		if ( 'all' === $args['content'] ) {
			unset( $args['content'] );
		} else {
			$args['post_type'] = $args['content'];
		}
	}
	return $args;
}

function wpcli_export_build_in_condition( $column_name, $values, $format = '%s' ) {
	global $wpdb;

	if ( ! is_array( $values ) || empty( $values ) ) {
		return '';
	}
	$formats         = implode( ', ', array_fill( 0, count( $values ), $format ) );
	$column_name_sql = Utils\esc_sql_ident( $column_name );
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- $column_name_sql escaped as ident, $formats hardcoded value.
	return $wpdb->prepare( "{$column_name_sql} IN ({$formats})", $values );
}
