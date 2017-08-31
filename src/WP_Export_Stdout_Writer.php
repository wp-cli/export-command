<?php

class WP_Export_Stdout_Writer extends WP_Export_Base_Writer {

	function __construct( $formatter, $writer_args ) {
		parent::__construct( $formatter );
		$this->before_posts_xml = $this->formatter->before_posts();
		$this->after_posts_xml = $this->formatter->after_posts();
	}

	public function export() {
		fwrite( STDOUT, $this->before_posts_xml );
		foreach( $this->formatter->posts() as $post_xml ) {
			fwrite( STDOUT, $post_xml );
		}
		fwrite( STDOUT, $this->after_posts_xml );
	}

	protected function write( $xml ) { }
}
