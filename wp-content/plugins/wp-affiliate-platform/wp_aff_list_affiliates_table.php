<?php
class WP_AFF_List_Affiliates_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'affiliate', //singular name of the listed records
            'plural'    => 'affiliates', //plural name of the listed records
            'ajax'      => false //does this table support ajax?
        ) );        
    }

    function column_default($item, $column_name){
    	//Just print the data for that column
    	return $item[$column_name];
    }
    
    function column_refid($item){
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="admin.php?page=affiliates_addedit&editaff=%s">Edit</a>',$item['refid']),
            'delete'    => sprintf('<a href="?page=%s&Delete=%s&delete_ref_id=%s" onclick="return confirm(\'Are you sure you want to delete this entry?\')">Delete</a>',$_REQUEST['page'],'1',$item['refid']),
        );
        
        //Return the refid column contents
        return $item['refid'] . $this->row_actions($actions);
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'], //Let's reuse singular label (affiliate)
            /*$2%s*/ $item['refid'] //The value of the checkbox should be the record's key/id
        );
    }
    
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'refid'     => 'Affiliate ID',
            'firstname'    => 'First Name',
            'lastname'  => 'Last Name',
            'email'  => 'Email Address',
            'date'  => 'Date Joined',
            'country'  => 'Country',
            'commissionlevel'  => 'Commission Level'
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'refid'     => array('refid',false),     //true means its already sorted
            'firstname'    => array('firstname',false),
            'lastname'  => array('lastname',false),
            'date'  => array('date',true),
            'commissionlevel'  => array('commissionlevel',false)
        );
        return $sortable_columns;
    }
    
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action() {        
        //Detect when a bulk action is being triggered... //print_r($_GET);
        if( 'delete'===$this->current_action() ) {
        	$affiliates_to_delete = $_GET['affiliate'];
        	if(empty($affiliates_to_delete)){
        		echo '<div id="message" class="updated fade"><p>Error! You need to select multiple records to perform a bulk action!</p></div>';
        		return;
        	}
        	foreach ($affiliates_to_delete as $refid){
			    global $wpdb;
				$affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;				
				$updatedb = "DELETE FROM $affiliates_table_name WHERE refid='$refid'";
				$results = $wpdb->query($updatedb);        		
        	} 
        	echo '<div id="message" class="updated fade"><p>Selected affiliate records deleted successfully!</p></div>';
        }        
    }    
    
    function prepare_items() {
        
        // Lets decide how many records per page to show         
        $per_page = 30;
                
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
                
        $this->_column_headers = array($columns, $hidden, $sortable);        
        
        $this->process_bulk_action();                              
        
        // This checks for sorting input and sorts the data.
        $orderby_column = isset($_GET['orderby'])?$_GET['orderby']:'';
        $sort_order = isset($_GET['order'])?$_GET['order']:'';
        if(empty($orderby_column)){
        	$orderby_column = "date";
        	$sort_order = "DESC";
        }
		global $wpdb;
		$affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
		$wp_aff_affiliates_db = $wpdb->get_results("SELECT * FROM $affiliates_table_name ORDER BY $orderby_column $sort_order", OBJECT);
		$data = array();
		$data = json_decode (json_encode ($wp_aff_affiliates_db), true);
		 
		//pagination requirement
        $current_page = $this->get_pagenum();
        
        //pagination requirement
        $total_items = count($data);        
        
        //pagination requirement
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);                
        
        // Now we add our *sorted* data to the items property, where it can be used by the rest of the class.
        $this->items = $data;        
        
        //pagination requirement
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }    
}
?>