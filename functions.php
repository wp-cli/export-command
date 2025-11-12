<?php

/**
 * @param array{filters?: array<mixed>, format?: class-string<WP_Export_WXR_Formatter>, writer?: class-string<WP_Export_Returner>, writer_args?: mixed} $args
 */
function wp_export( $args = array() ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Renaming breaks Phar compat.
	$defaults = array(
		'filters'     => array(),
		'format'      => 'WP_Export_WXR_Formatter',
		'writer'      => 'WP_Export_Returner',
		'writer_args' => null,
	);

	/**
	 * @var array{filters: array<mixed>, format: class-string<WP_Export_WXR_Formatter>, writer: class-string<WP_Export_Returner>, writer_args: mixed} $args
	 */
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

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound,WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid -- Renaming breaks Phar compat.
function _wp_export_build_IN_condition( $column_name, $values, $format = '%s' ) {
	global $wpdb;

	if ( ! is_array( $values ) || empty( $values ) ) {
		return '';
	}
	$formats = implode( ', ', array_fill( 0, count( $values ), $format ) );
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- $column_name_sql escaped as ident, $formats hardcoded value.
	return $wpdb->prepare( "{$column_name} IN ({$formats})", $values );
}
