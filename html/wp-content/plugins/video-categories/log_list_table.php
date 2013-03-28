<?php
/*
  Plugin Name: Test List Table Example
 */

if (!class_exists('WP_List_Table')) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Log_List_Table extends WP_List_Table {

	protected $logs;

	function __construct() {
		global $wpdb;

		parent::__construct(array(
				'singular' => __('log', 'log_list_table'),
				'plural' => __('logs', 'log_list_table'),
				'ajax' => false
		));
		if ((isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') ||
						(isset($_REQUEST['action2']) && $_REQUEST['action2'] == 'delete')) {
			$search_content = $_REQUEST['search_content'];

			if (is_array($search_content)) {
				$wpdb->query('DELETE FROM ' . VIDEO_CATEGORY_LOG_TABLE . ' WHERE search_content IN ("' . implode('","', $search_content) . '")');
			} else {
				$wpdb->query($wpdb->prepare('DELETE FROM ' . VIDEO_CATEGORY_LOG_TABLE . ' WHERE search_content = %s', $search_content));
			}
		}
		$this->logs = $wpdb->get_results('SELECT * FROM ' . VIDEO_CATEGORY_LOG_TABLE, ARRAY_A);
	}

	function admin_header() {
		$page = ( isset($_GET['page']) ) ? esc_attr($_GET['page']) : false;

		if ('log-search-result' != $page)
			return;
		echo '<style type="text/css">';
		echo '.wp-list-table .column-id { width: 5%; }';
		echo '.wp-list-table .column-search_content { width: 40%;font-weight: bold;color:#21759B; }';
		echo '.wp-list-table .column-date { width: 35%; }';
		echo '.wp-list-table .column-count { width: 20%;}';
		echo '.wp-list-table tbody td{vertical-align: middle;}';
		echo '.wp-list-table tbody th input{vertical-align: middle;}';
		echo '</style>';
	}

	function no_items() {
		_e('<center>No logs found.</center>');
	}

	function column_default($item, $column_name) {
		switch ($column_name) {
			case 'search_content':
			case 'date':
			case 'count':
				return $item[$column_name];
			default:
				return print_r($item, true); //Show the whole array for troubleshooting purposes
		}
	}

	function get_sortable_columns() {
		$sortable_columns = array(
				'search_content' => array('search_content', false),
				'date' => array('date', false),
				'count' => array('count', false)
		);
		return $sortable_columns;
	}

	function get_columns() {
		$columns = array(
				'cb' => '<input type="checkbox" />',
				'search_content' => __('Search Content', 'mylisttable'),
				'date' => __('Last Search Date', 'mylisttable'),
				'count' => __('Count', 'mylisttable')
		);
		return $columns;
	}

	function usort_reorder($a, $b) {
		// If no sort, default to search_content
		$orderby = (!empty($_GET['orderby']) ) ? $_GET['orderby'] : 'search_content';
		// If no order, default to asc
		$order = (!empty($_GET['order']) ) ? $_GET['order'] : 'asc';
		// Determine sort order
		$result = strcmp($a[$orderby], $b[$orderby]);
		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : -$result;
	}

	function get_bulk_actions() {
		$actions = array(
				'delete' => 'Delete'
		);
		return $actions;
	}

	function column_search_content($item) {
		$actions = array(
				'delete' => sprintf('<a href="?page=%s&action=%s&search_content=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['search_content']),
		);
		return sprintf('%1$s %2$s', $item['search_content'], $this->row_actions($actions));
	}

	function column_cb($item) {
		return sprintf('<input type="checkbox" name="search_content[]" value="%s" />', $item['search_content']);
	}

	function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		usort($this->logs, array(&$this, 'usort_reorder'));

		$per_page = 20;
		$current_page = $this->get_pagenum();
		$total_items = count($this->logs);

		// only ncessary because we have sample data
		$this->found_data = array_slice($this->logs, ( ( $current_page - 1 ) * $per_page), $per_page);

		$this->set_pagination_args(array(
				'total_items' => $total_items, //WE have to calculate the total number of items
				'per_page' => $per_page //WE have to determine how many items to show on a page
		));
		$this->items = $this->found_data;
	}

	function search_box($text, $input_id) {
		global $wpdb;

		if (empty($_REQUEST['s'])) {
			$this->logs = $wpdb->get_results('SELECT * FROM ' . VIDEO_CATEGORY_LOG_TABLE, ARRAY_A);
		} else {
			$this->logs = $wpdb->get_results('SELECT * FROM ' . VIDEO_CATEGORY_LOG_TABLE . ' WHERE search_content LIKE "%' . $_REQUEST['s'] . '%"', ARRAY_A);
		}

		$input_id = $input_id . '-search-input';

		if (!empty($_REQUEST['orderby']))
			echo '<input type="hidden" name="orderby" value="' . esc_attr($_REQUEST['orderby']) . '" />';
		if (!empty($_REQUEST['order']))
			echo '<input type="hidden" name="order" value="' . esc_attr($_REQUEST['order']) . '" />';
		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
			<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
			<?php submit_button($text, 'button', false, false, array('id' => 'search-submit')); ?>
		</p>
		<?php
	}

}