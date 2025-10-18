<?php

/**
 * @implements \Iterator<int, object{}>
 */
class WP_Post_IDs_Iterator implements Iterator {
	/**
	 * @var \wpdb
	 */
	private $db;

	/**
	 * @var int
	 */
	private $limit = 100;

	/**
	 * @var int[]
	 */
	private $post_ids;

	/**
	 * @var int[]
	 */
	private $ids_left;

	/**
	 * @var int[]
	 */
	private $results = array();

	/**
	 * @var int
	 */
	private $index_in_results;

	/**
	 * @var int
	 */
	private $global_index;

	public function __construct( $post_ids, $limit = null ) {
		$this->db       = $GLOBALS['wpdb'];
		$this->post_ids = $post_ids;
		$this->ids_left = $post_ids;
		if ( ! is_null( $limit ) ) {
			$this->limit = $limit;
		}
	}

	#[\ReturnTypeWillChange]
	public function current() {
		return $this->results[ $this->index_in_results ];
	}

	#[\ReturnTypeWillChange]
	public function key() {
		return $this->global_index;
	}

	#[\ReturnTypeWillChange]
	public function next() {
		++$this->index_in_results;
		++$this->global_index;
	}

	#[\ReturnTypeWillChange]
	public function rewind() {
		$this->results          = array();
		$this->global_index     = 0;
		$this->index_in_results = 0;
		$this->ids_left         = $this->post_ids;
	}

	#[\ReturnTypeWillChange]
	public function valid() {
		if ( isset( $this->results[ $this->index_in_results ] ) ) {
			return true;
		}
		if ( empty( $this->ids_left ) ) {
			return false;
		}
		$has_posts = $this->load_next_posts_from_db();
		if ( ! $has_posts ) {
			return false;
		}
		$this->index_in_results = 0;
		return true;
	}

	private function load_next_posts_from_db() {
		$next_batch_post_ids = array_splice( $this->ids_left, 0, $this->limit );
		$in_post_ids_sql     = _wp_export_build_IN_condition( 'ID', $next_batch_post_ids );
		$this->results       = $this->db->get_results( "SELECT * FROM {$this->db->posts} WHERE {$in_post_ids_sql}" );
		if ( ! $this->results ) {
			if ( $this->db->last_error ) {
				throw new WP_Iterator_Exception( "Database error: {$this->db->last_error}" );
			} else {
				return false;
			}
		}
		return true;
	}
}
