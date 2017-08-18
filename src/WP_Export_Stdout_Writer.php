<?php

class WP_Export_Stdout_Writer extends WP_Export_Base_Writer {

	function __construct( $formatter, $writer_args = array() ) {
		parent::__construct( $formatter );
		//TODO: check if args are not missing
		$this->before_posts_xml = $this->formatter->before_posts();
		$this->after_posts_xml = $this->formatter->after_posts();
	}

	public function export() {
		// WP_CLI\Utils\wp_clear_object_cache(); ?
		fwrite( STDOUT, $this->before_posts_xml );
		foreach( $this->formatter->posts() as $post_xml ) {
			fwrite( STDOUT, $post_xml );
		}
		fwrite( STDOUT, $this->after_posts_xml );
	}

	protected function write( $xml ) { }
}
