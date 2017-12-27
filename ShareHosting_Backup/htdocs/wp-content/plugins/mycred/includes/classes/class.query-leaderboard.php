<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Query Log
 * @see http://codex.mycred.me/classes/mycred_query_leaderboard/ 
 * @since 1.7.9.1
 * @version 1.0
 */
if ( ! class_exists( 'myCRED_Query_Leaderboard' ) ) :
	class myCRED_Query_Leaderboard {

		public $id             = '';
		public $now            = 0;
		public $core           = NULL;
		public $user_id        = 0;
		private $max_size      = 250;

		public $args           = array();
		public $based_on       = 'balance';
		public $references     = array();
		public $order          = '';
		public $limit          = '';

		public $leaderboard    = false;

		/**
		 * Construct
		 * Preps the class for getting a leaderboard based on the
		 * given arguments. Validates these arguments.
		 * @since 1.0
		 * @version 1.0.1
		 */
		public function __construct( $args = array() ) {

			if ( ! MYCRED_ENABLE_LOGGING ) return false;

			// Generate a unique ID that identifies the leaderboard we are trying to build
			$this->id       = md5( implode( '|', $args ) );

			// Parse and validate the given args
			$this->parse_args( $args );

			$this->now      = current_time( 'timestamp' );
			$this->core     = mycred( $this->args['type'] );
			$this->user_id  = get_current_user_id();
			$this->max_size = apply_filters( 'mycred_max_leaderboard_size', 250, $this );

			// What is the leaderboard based on
			$this->based_on = $this->args['based_on'];
			$this->order    = $this->args['order'];

			// Setup limit
			if ( $this->args['number'] > 0 ) {

				$this->limit = 'LIMIT ' . $this->args['number'];
				if ( $this->args['offset'] != 0 )
					$this->limit = 'LIMIT ' . $this->args['offset'] . ', ' . $this->args['number'];

			}

		}

		/**
		 * Parse Arguments
		 * We have two jobs: Make sure we provide arguments we can understand and
		 * that the arguments we provided are valid.
		 * @since 1.0
		 * @version 1.0
		 */
		public function parse_args( $args = array() ) {

			/**
			 * Populate Query Arguments
			 * @uses mycred_query_leaderboard_args
			 * @see http://codex.mycred.me/filters/mycred_query_leaderboard_args/
			 */
			$defaults             = array(
				'based_on'     => 'balance',
				'number'       => 25,
				'offset'       => 0,
				'type'         => MYCRED_DEFAULT_TYPE_KEY,
				'timeframe'    => '',
				'order'        => 'DESC',
				'total'        => 0,
				'exclude_zero' => 1,
				'forced'       => 0
			);
			$args                 = apply_filters( 'mycred_query_leaderboard_args', shortcode_atts( $defaults, $args ), $args, $this );

			// Based on
			$based_on             = sanitize_text_field( $args['based_on'] );
			if ( $based_on != 'balance' ) {

				$references = array();
				if ( ! empty( $args['based_on'] ) ) {
					foreach ( explode( ',', $based_on ) as $ref ) {
						$ref = sanitize_key( $ref );
						if ( strlen( $ref ) == 0 ) continue;
						$references[] = $ref;
					}
					$this->references = $references;
				}

				$based_on = 'references';

			}

			$args['based_on']     = $based_on;

			// Number or leaderboard size
			$number               = (int) sanitize_key( $args['number'] );
			if ( $number < -1 )
				$number = -1;

			elseif ( ! is_numeric( $number ) )
				$number = 25;

			elseif ( $number > $this->max_size )
				$number = $this->max_size;

			$args['number']       = $number;

			// Option to offset
			$offset               = (int) sanitize_key( $args['offset'] );
			if ( ! is_numeric( $offset ) )
				$offset = 0;

			$args['offset']       = $offset;

			// Point Type
			$point_type           = sanitize_key( $args['type'] );
			if ( ! mycred_point_type_exists( $point_type ) )
				$point_type = MYCRED_DEFAULT_TYPE_KEY;

			$args['type']         = $point_type;

			// Timeframe
			$args['timeframe']    = sanitize_text_field( $args['timeframe'] );

			// Order
			$order = strtoupper( sanitize_text_field( $args['order'] ) );
			if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) )
				$order = 'DESC';

			$args['order']        = $order;

			// Show total balance
			$args['total']        = (bool) $args['total'];

			// Exclude zero balances
			$args['exclude_zero'] = (bool) $args['exclude_zero'];

			// Force a new leaderboard instead of a cached one (if used)
			$args['forced']       = (bool) $args['forced'];

			$this->args           = $args;

		}

		/**
		 * Get Leaderboard Results
		 * Returns the leaderboard data in an array form or false if the query results in no data.
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_leaderboard_results( $append_current_user = false ) {

			$results = $this->get_cache();
			if ( $results === false ) {

				global $wpdb;

				$results = $wpdb->get_results( $this->get_db_query(), 'ARRAY_A' );
				if ( empty( $results ) )
					$results = false;

			}

			if ( $results !== false )
				$this->cache_result( $results );

			$this->leaderboard = $results;

			if ( $append_current_user )
				$this->append_current_user();

			$results           = $this->leaderboard;
			$this->leaderboard = apply_filters( 'mycred_get_leaderboard_results', $results, $append_current_user, $this );

		}

		/**
		 * Append Current User
		 * Appends the current logged in user to the end of the leaderboard if the user is not in the results.
		 * This is done separatelly since we can not cache a leaderboard for each user that might view the board.
		 * @since 1.0
		 * @version 1.0
		 */
		public function append_current_user( $return = false ) {

			if ( ! is_user_logged_in() || $this->leaderboard === false || $this->core->exclude_user( $this->user_id ) ) return;

			// First we need to check if the user is already in the leaderboard
			if ( $this->user_in_leaderboard() ) return;

			// User is not in the leaderboard so we need to append him/her to the end of the leaderboard array.
			$new_row             = array( 'ID' => $this->user_id );
			$new_row['position'] = $this->get_users_current_position();
			$new_row['cred']     = $this->get_users_current_value();

			if ( $return )
				return $new_row;

			$this->leaderboard[] = $new_row;

		}

		/**
		 * User In Leaderboard
		 * Checks if a given user or the current user is in the leaderboard.
		 * @since 1.0
		 * @version 1.0
		 */
		public function user_in_leaderboard( $user_id = NULL ) {

			$in_leaderboard = false;
			if ( $this->leaderboard !== false && ! empty( $this->leaderboard ) ) {

				if ( $user_id === NULL || absint( $user_id ) === 0 )
					$user_id = $this->user_id;

				$user_id = absint( $user_id );

				foreach ( $this->leaderboard as $position => $user ) {
					if ( absint( $user['ID'] ) === $user_id ) {
						$in_leaderboard = true;
						break;
					}
				}

			}

			return apply_filters( 'mycred_user_in_leaderboard', $in_leaderboard, $user_id, $this );

		}

		/**
		 * Get Database Query
		 * Returns the SQL query required for generating a leaderboard.
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_db_query() {

			if ( $this->based_on == 'balance' )
				$query = $this->get_balance_db_query();
			else
				$query = $this->get_reference_db_query();

			return $query;

		}

		/**
		 * Get Balance Database Query
		 * Returns the SQL query required for generating a leaderboard that is based on balances.
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_balance_db_query() {

			global $wpdb, $mycred_log_table;

			$query           = '';
			$time_filter     = $this->get_timefilter();
			$exclude_filter  = $this->get_excludefilter();
			$multisite_check = $this->get_multisitefilter();

			// Total balance
			if ( $this->args['total'] ) {

				$query = $wpdb->prepare( "
					SELECT l.user_id AS ID, SUM( l.creds ) AS cred 
					FROM {$mycred_log_table} l 
					{$multisite_check} 
					WHERE l.ctype = %s AND ( ( l.creds > 0 ) OR ( l.creds < 0 AND l.ref = 'manual' ) ) 
					{$time_filter}
					{$exclude_filter} 
					GROUP BY l.user_id
					ORDER BY SUM( l.creds ) {$this->order}, l.user_id ASC 
					{$this->limit};", $this->args['type'] );

			}

			// Current Balance
			else {

				$query = $wpdb->prepare( "
					SELECT DISTINCT u.ID, l.meta_value AS cred 
					FROM {$wpdb->users} u 
					INNER JOIN {$wpdb->usermeta} l ON ( u.ID = l.user_id ) 
					{$multisite_check} 
					WHERE l.meta_key = %s 
					{$exclude_filter} 
					ORDER BY l.meta_value+0 {$this->order}, l.user_id ASC
					{$this->limit};", mycred_get_meta_key( $this->args['type'] ) );

			}

			return apply_filters( 'mycred_get_balance_leaderboard_sql', $query, $this );

		}

		/**
		 * Get Balance Database Query
		 * Returns the SQL query required for generating a leaderboard that is based on references.
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_reference_db_query() {

			global $wpdb, $mycred_log_table;

			$time_filter     = $this->get_timefilter();
			$multisite_check = $this->get_multisitefilter();

			$reference   = 'l.ref = %s';
			if ( count( $this->references ) > 1 )
				$reference = 'l.ref IN ( %s' . str_repeat( ', %s', ( count( $this->references ) - 1 ) ) . ' )';

			if ( mycred_centralize_log() )
				$query = $wpdb->prepare( "SELECT DISTINCT l.user_id AS ID, SUM( l.creds ) AS cred FROM {$mycred_log_table} l WHERE {$reference} {$time_filter} GROUP BY l.user_id ORDER BY SUM( l.creds ) {$this->order} {$this->limit};", $this->references );

			// Multisite support
			else {

				$blog_id = absint( $GLOBALS['blog_id'] );
				$query   = $wpdb->prepare( "
					SELECT DISTINCT l.user_id AS ID, SUM( l.creds ) AS cred 
					FROM {$mycred_log_table} l 
					{$multisite_check} 
					WHERE {$reference} 
					{$time_filter} 
					GROUP BY l.user_id 
					ORDER BY SUM( l.creds ) {$this->order}, l.user_id ASC
					{$this->limit};", $this->references );

			}

			return apply_filters( 'mycred_get_reference_leaderboard_sql', $query, $this );

		}

		/**
		 * Get Users Leaderboard Position
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_users_current_position( $user_id = NULL, $no_position = '' ) {

			$position    = false;
			// Better safe than sorry
			if ( $user_id === NULL && ! is_user_logged_in() ) return $position;

			if ( $user_id === NULL || absint( $user_id ) === 0 )
				$user_id = $this->user_id;

			global $wpdb, $mycred_log_table;

			$time_filter     = $this->get_timefilter();
			$exclude_filter  = $this->get_excludefilter();
			$multisite_check = $this->get_multisitefilter();

			if ( $this->based_on == 'balance' ) {

				// Current Balance
				if ( $this->args['total'] ) {

					$position = $wpdb->get_var( $wpdb->prepare( "
						SELECT rank FROM (
							SELECT s.*, @rank := @rank + 1 rank FROM (
								SELECT l.user_id, sum( l.creds ) TotalPoints FROM {$mycred_log_table} l 
								{$multisite_check}
								WHERE l.ctype = %s AND ( ( l.creds > 0 ) OR ( l.creds < 0 AND l.ref = 'manual' ) ) 
								{$time_filter} 
								{$exclude_filter} 
								GROUP BY l.user_id
								) s, (SELECT @rank := 0) init
							ORDER BY TotalPoints DESC, s.user_id ASC 
						) r 
						WHERE user_id = %d", $this->args['type'], $user_id ) );

				}

				else {

					$position = $wpdb->get_var( $wpdb->prepare( "
						SELECT rank FROM (
							SELECT s.*, @rank := @rank + 1 rank FROM (
								SELECT l.user_id, l.meta_value AS Balance FROM {$wpdb->usermeta} l 
								{$multisite_check} 
								WHERE l.meta_key = %s 
								{$exclude_filter}
							) s, (SELECT @rank := 0) init
							ORDER BY Balance+0 DESC, s.user_id ASC 
						) r 
						WHERE user_id = %d", mycred_get_meta_key( $this->args['type'] ), $user_id ) );

				}

			}

			else {

				$reference   = 'l.ref = %s';
				if ( count( $this->references ) > 1 )
					$reference = 'l.ref IN ( %s' . str_repeat( ', %s', ( count( $this->references ) - 1 ) ) . ' )';

				$position    = $wpdb->get_var( $wpdb->prepare( "
					SELECT rank FROM (
						SELECT s.*, @rank := @rank + 1 rank FROM (
							SELECT l.user_id, sum( l.creds ) TotalPoints FROM {$mycred_log_table} l 
							{$multisite_check}
							WHERE l.ctype = %s AND ( ( l.creds > 0 ) OR ( l.creds < 0 AND l.ref = 'manual' ) ) 
							{$reference} 
							{$time_filter} 
							{$exclude_filter} 
							GROUP BY l.user_id
						) s, (SELECT @rank := 0) init
						ORDER BY TotalPoints DESC, s.user_id ASC 
					) r 
					WHERE user_id = %d", $this->args['type'], $user_id ) );

			}

			if ( $position === NULL )
				$position = $no_position;

			return apply_filters( 'mycred_get_leaderboard_position', $position, $user_id, $no_position, $this );

		}

		/**
		 * Get Users Leaderboard Value
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_users_current_value( $user_id = NULL ) {

			if ( $user_id === NULL || absint( $user_id ) === 0 )
				$user_id = $this->user_id;

			global $wpdb, $mycred_log_table;

			$time_filter     = $this->get_timefilter();
			$multisite_check = $this->get_multisitefilter();

			if ( $this->based_on == 'balance' ) {

				$value = $this->core->get_users_balance( $user_id );
				if ( $this->args['total'] ) {

					if ( $this->args['timeframe'] == '' )
						$value = mycred_query_users_total( $user_id, $this->args['type'] );

					else {

						$value = $wpdb->get_var( $wpdb->prepare( "
							SELECT SUM( l.creds ) 
							FROM {$mycred_log_table} l 
							{$multisite_check} 
							WHERE l.ctype = %s AND ( ( l.creds > 0 ) OR ( l.creds < 0 AND l.ref = 'manual' ) ) 
							AND user_id = %d 
							{$time_filter};", $this->args['type'], $user_id ) );

						if ( $value === NULL ) $value = 0;

					}

				}

			}

			else {

				$reference   = 'l.ref = %s';
				if ( count( $this->references ) > 1 )
					$reference = 'l.ref IN ( %s' . str_repeat( ', %s', ( count( $this->references ) - 1 ) ) . ' )';

				if ( mycred_centralize_log() )
					$query = $wpdb->prepare( "SELECT SUM( l.creds ) FROM {$mycred_log_table} l WHERE {$reference} AND user_id = %d {$time_filter};", $this->references, $user_id );

				// Multisite support
				else {

					$blog_id = absint( $GLOBALS['blog_id'] );
					$query   = $wpdb->prepare( "
						SELECT SUM( l.creds ) 
						FROM {$mycred_log_table} l 
						{$multisite_check} 
						WHERE {$reference} 
						AND user_id = %d
						{$time_filter};", $this->references, $user_id );

				}

				$value = $wpdb->get_var( $query );
				if ( $value === NULL ) $value = 0;

			}

			return apply_filters( 'mycred_get_users_leaderboard_value', $value, $user_id, $this );

		}

		/**
		 * Get Time Filter
		 * Generates the required SQL query for filtering results based on time.
		 * Can only be used when the leaderboard is based either on total balance or based on references.
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_timefilter() {

			$query = '';
			if ( $this->args['timeframe'] === NULL || strlen( $this->args['timeframe'] ) == 0 ) return $query;

			global $wpdb;

			// Start of the week based of our settings
			$week_starts = get_option( 'start_of_week' );
			if ( $week_starts == 0 )
				$week_starts = 'sunday';
			else
				$week_starts = 'monday';

			// Filter: Daily
			if ( $this->args['timeframe'] == 'today' )
				$query = $wpdb->prepare( "AND l.time BETWEEN %d AND %d", strtotime( 'today midnight', $this->now ), $this->now );

			// Filter: Weekly
			elseif ( $this->args['timeframe'] == 'this-week' )
				$query = $wpdb->prepare( "AND l.time BETWEEN %d AND %d", strtotime( $week_starts . ' this week', $this->now ), $this->now );

			// Filter: Monthly
			elseif ( $this->args['timeframe'] == 'this-month' )
				$query = $wpdb->prepare( "AND l.time BETWEEN %d AND %d", strtotime( date( 'Y-m-01', $this->now ) ), $this->now );

			else {

				$start_from = strtotime( $this->args['timeframe'], $this->now );
				if ( $start_from !== false && $start_from > 0 )
					$query = $wpdb->prepare( "AND l.time BETWEEN %d AND %d", $start_from, $this->now );

			}

			return apply_filters( 'mycred_leaderboard_time_filter', $query, $this );

		}

		/**
		 * Get Exclude Filter
		 * Generates the required SQL query for filtering results based on if zero balances should
		 * be part of the leaderboard or not. By default, myCRED will not give a user a balance until they
		 * gain or lose points. A user that has no balance and is not excluded, is considered to have zero balance.
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_excludefilter() {

			global $wpdb;

			// Option to exclude zero balances
			$query = '';
			if ( $this->args['exclude_zero'] ) {

				$balance_format = '%d';
				if ( isset( $this->core->format['decimals'] ) && $this->core->format['decimals'] > 0 ) {
					$length         = absint( 65 - $this->core->format['decimals'] );
					$balance_format = 'CAST( %f AS DECIMAL( ' . $length . ', ' . $this->core->format['decimals'] . ' ) )';
				}

				if ( ! $this->args['total'] )
					$query = $wpdb->prepare( "AND l.meta_value != {$balance_format}", $this->core->zero() );

			}

			return apply_filters( 'mycred_leaderboard_exclude_filter', $query, $this );

		}

		/**
		 * Get Multisite Filter
		 * Generates the required SQL query for filtering results based on our multisite setup.
		 * Will return an empty string if we are not using multisites or if we have centralized the log.
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_multisitefilter() {

			global $wpdb;

			$multisite_check = "";
			if ( ! mycred_centralize_log() ) {

				$blog_id         = absint( $GLOBALS['blog_id'] );
				$multisite_check = "LEFT JOIN {$wpdb->usermeta} cap ON ( l.user_id = cap.user_id AND cap.meta_key = 'cap.wp_{$blog_id}_capabilities' )";

			}

			return apply_filters( 'mycred_leaderboard_musite_filter', $multisite_check, $this );

		}

		/**
		 * Get Cache Key
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_cache_key() {

			return 'mycred-leaderboard-' . md5( implode( '|', $this->args ) );

		}

		/**
		 * Get Cached Leaderboard
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_cache() {

			if ( $this->args['forced'] == 1 || MYCRED_DISABLE_LEADERBOARD_CACHE ) return false;

			$cached_results = get_option( $this->get_cache_key(), false );
			if ( $cached_results !== false )
				$cached_results = maybe_unserialize( $cached_results );

			return $cached_results;

		}

		/**
		 * Cache Results
		 * @since 1.0
		 * @version 1.0
		 */
		public function cache_result( $data = array() ) {

			if ( MYCRED_DISABLE_LEADERBOARD_CACHE ) return;

			if ( $this->to_big_to_cache() ) return;

			update_option( $this->get_cache_key(), $data );

		}

		/**
		 * Check if the cache is too big for storage in the db.
		 * @since 1.0
		 * @version 1.0
		 */
		public function to_big_to_cache() {

			$big = false;
			if ( $this->leaderboard !== false ) {

				

			}

			return $big;

		}

		/**
		 * Is Leaderboard
		 * @since 1.0
		 * @version 1.0
		 */
		public function is_leaderboard( $args = array() ) {

			return ( $this->id == md5( implode( '|', $args ) ) );

		}

		/**
		 * Render Leaderboard
		 * @since 1.0
		 * @version 1.0
		 */
		public function render( $args = array(), $content = '' ) {

			extract( shortcode_atts( array(
				'wrap'         => 'li',
				'template'     => '#%position% %user_profile_link% %cred_f%',
				'nothing'      => 'Leaderboard is empty',
			), $args ) );

			$output = '';

			// Leaderboard is empty
			if ( $this->leaderboard === false || empty( $this->leaderboard ) ) {

				$output .= '<p class="mycred-leaderboard-none">' . $nothing . '</p>';

			}

			// Got results to show
			else {

				// Wrapper
				if ( $wrap == 'li' )
					$output .= '<ol class="myCRED-leaderboard list-unstyled">';

				// Loop
				foreach ( $this->leaderboard as $position => $user ) {

					// Prep
					$class   = array();
					$row     = $position;

					if ( array_key_exists( 'position', $user ) )
						$position = $user['position'];

					else {

						if ( $this->args['offset'] != '' && $this->args['offset'] > 0 )
							$position = $position + $this->args['offset'];

						$position++;

					}

					// Classes
					$class[] = 'item-' . $row;
					if ( $position == 0 )
						$class[] = 'first-item';

					if ( $this->user_id > 0 && $user['ID'] == $this->user_id )
						$class[] = 'current-user';

					if ( $position % 2 != 0 )
						$class[] = 'alt';

					$row_template = $template;
					if ( ! empty( $content ) )
						$row_template = $content;

					// Template Tags
					$layout  = str_replace( array( '%ranking%', '%position%' ), $position, $row_template );

					$layout  = $this->core->template_tags_amount( $layout, $user['cred'] );
					$layout  = $this->core->template_tags_user( $layout, $user['ID'] );

					// Wrapper
					if ( ! empty( $wrap ) )
						$layout = '<' . $wrap . ' class="%classes%">' . $layout . '</' . $wrap . '>';

					$layout  = str_replace( '%classes%', apply_filters( 'mycred_ranking_classes', implode( ' ', $class ), $this ), $layout );
					$layout  = apply_filters( 'mycred_ranking_row', $layout, $template, $user, $position, $this );

					$output .= $layout . "\n";

				}

				if ( $wrap == 'li' )
					$output .= '</ol>';

			}

			return apply_filters( 'mycred_leaderboard', $output, $args, $this );

		}

	}
endif;

/**
 * Get Leaderboard
 * @since 1.7.9.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_leaderboard' ) ) :
	function mycred_get_leaderboard( $args = array() ) {

		global $mycred_leaderboard;

		// No need to do extra work if we already have the correct global
		if ( is_object( $mycred_leaderboard ) && $mycred_leaderboard->is_leaderboard( $args ) )
			return $mycred_leaderboard;

		$mycred_leaderboard = new myCRED_Query_Leaderboard( $args );

		return $mycred_leaderboard;

	}
endif;
