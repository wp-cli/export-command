<?php

class WP_Export_Oxymel extends Oxymel {
	public function optional( $tag_name, $contents ) {
		if ( $contents ) {
			$this->$tag_name( $contents );
		}
		return $this;
	}

	public function optional_cdata( $tag_name, $contents ) {
		if ( $contents ) {
			$this->$tag_name->contains->cdata( $contents )->end;
		}
		return $this;
	}

	public function cdata( $text ) {
		if ( is_string( $text ) ) {
			if ( function_exists( 'wp_is_valid_utf8' ) ) {
				if ( ! wp_is_valid_utf8( $text ) ) {
					$text = mb_convert_encoding( $text, 'UTF-8' );
				}
			} elseif ( ! seems_utf8( $text ) ) { // phpcs:ignore WordPress.WP.DeprecatedFunctions.seems_utf8Found
				$text = mb_convert_encoding( $text, 'UTF-8' );

			}
		}
		return parent::cdata( $text );
	}
}
