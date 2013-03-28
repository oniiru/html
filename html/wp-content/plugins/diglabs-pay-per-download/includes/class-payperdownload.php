<?php

class PayPerDownload {

	private $table_prefix = null;
	
	private $slug = "dlppd";
	
	private function get_table_prefix() {
		if(is_null($this->table_prefix)) {
			global $wpdb;
			$this->table_prefix = $wpdb->prefix . "dlppd_";
		}
		return $this->table_prefix;
	}
		
	private function get_table_name($table_name) {
		return $this->get_table_prefix() . $table_name;
	}
	private function get_products_table_name() {
		return $this->get_table_name("products");
	}
	private function get_payments_table_name() {
		return $this->get_table_name("payments");
	}
	
	public function ensure_db_tables_exists() {
		global $wpdb;
		
		$table_name = $this->get_products_table_name();
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			echo "HERE-$table_name<br />";
			$sql = "CREATE TABLE ".$table_name." (
					id INT UNSIGNED NOT NULL AUTO_INCREMENT,
					name VARCHAR(255) NOT NULL,
					cost DECIMAL(7, 2) NOT NULL,
					file VARCHAR(255) NOT NULL,
					PRIMARY KEY  id (id)
				);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
		
		$table_name = $this->get_payments_table_name();
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			echo "HERE-$table_name<br />";
			$sql = "CREATE TABLE ".$table_name." (
					id VARCHAR(255) NOT NULL,
					count mediumint(9) DEFAULT '0' NOT NULL,
					file VARCHAR(255) NOT NULL,
					prod_id VARCHAR(255) NOT NULL,
					email VARCHAR(255) NOT NULL,
					PRIMARY KEY  id (id)
				);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}
	
	// product table crud
	
	public function add_product($name, $cost, $file) {
		global $wpdb;
		$tbl_name = $this->get_products_table_name();
		$data = array(
			'name'	=> $name,
			'cost'	=> $cost,
			'file'	=> $file
		);
		return $rows_affected = $wpdb->insert($tbl_name, $data);
	}
	
	public function all_products() {
		global $wpdb;
		$tbl_name = $this->get_products_table_name();
		$sql = "SELECT * FROM $tbl_name ORDER BY name";
		return $wpdb->get_results($sql, ARRAY_A);
	}
	
	public function get_product($id) {
		global $wpdb;
		$tbl_name = $this->get_products_table_name();
		$sql = $wpdb->prepare("SELECT * FROM $tbl_name WHERE id='%s'", $id);
		return $wpdb->get_row($sql, ARRAY_A);
	}
	
	public function delete_product($id) {
		global $wpdb;
		
		$tbl_name = $this->get_products_table_name();
		$sql = $wpdb->prepare("DELETE FROM $tbl_name WHERE id='%s'", $id);
		echo "sql=$sql<br />";
		return $wpdb->query($sql);
	}
	
	// payment table crud
	
	public function add_payment($id, $count, $file, $prod_id, $email) {
		global $wpdb;
		
		$table_name = $this->get_payments_table_name();
		$data = array(
			'id'		=> $id, 
			'count'		=> $count, 
			'file'		=> $file,
			'prod_id'	=> $prod_id,
			'email'		=> $email
			);
		return $rows_affected = $wpdb->insert($table_name, $data);
	}
	
	public function get_payment($id) {
		global $wpdb;
		
		$table_name = $this->get_payments_table_name();
		$sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id='%s'", $id);
		return $wpdb->get_row($sql, ARRAY_A);
	}
	
	public function get_product_download_url($id) {
		$url = untrailingslashit( site_url() ) . "/" .$this->slug . "/" . $id;
		return $url;
	}
			
	public function get_product_download_path($id) {
		$payment = $this->get_payment($id);
		if(is_null($payment)){
			return null;
		}
		$count = $payment['count'];
		if(!is_numeric($count)){
			return null;
		}
		$count = intval($count);
		if($count<=0){
			return null;
		}
		
		// decrement the count
		$new_count = $count - 1;
		$this->decrement_count($id, $new_count);
		
		// stitch the path together
		$path = dirname(__FILE__).'/../files/';
		return $path . $payment['file'];
	}

	private function decrement_count($id, $count) {
		global $wpdb;
		
		$table_name = $this->get_payments_table_name();
		return $wpdb->update(
				$table_name,
				array( 'count'	=> $count ),
				array( 'id'	=> $id ),
				array( '%d' ),
				array( '%s' )
			);
	}
}
?>