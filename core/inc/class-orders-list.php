<?php
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Orders_list extends WP_List_Table{

    function __construct(){
		
        global $status, $page;

        parent::__construct(array(
            'singular' => 'person',
            'plural' => 'persons',
        ));
    }

    function column_default($item, $column_name){
        return $item[$column_name];
		
    }
	
	function column_order_name_surname($item){
		
		global $wpdb;


		$orderid = $item['order_id'];
		$tablename = $wpdb->prefix . "WPBEGPAY_orders";

		$getorder = $wpdb->get_results("SELECT * FROM  $tablename WHERE order_id = $orderid" );

		if ( $getorder ){
			
			foreach ($getorder as $order) { 
					$actions = array(
						'view' => sprintf('<a href="%s">%s</a>', admin_url( 'admin.php?page=bank-mellat&orderId=' . $item['order_id'], 'http' ), 'نمایش جزئیات'),
						'delete' => sprintf('<a href="%s">%s</a>', admin_url( 'admin.php?page=bank-mellat&action=delete&id=' . $item['order_id'], 'http' ), 'حذف'),
					);
			}
		}
		
		return sprintf('%s %s',
			$item['order_name_surname'],
			$this->row_actions($actions)
		);
    }

 
    function column_order_amount($item){
        return '<em>' . number_format($item['order_amount']) . '</em>';
		
    }
	
    function column_order_status($item){
       
	   return ($item['order_status'] == 'yes') ? 'انجام شده است' : 'انجام نشده است';		
    }
	
    function column_order_settle($item){
       
	   return ($item['order_settle'] == 'yes') ? 'انجام شده است' : 'انجام نشده است';		
    }
   
    function get_columns(){
		
        $columns = array(
            'order_id' => '#', 
			'order_name_surname' =>'نام&nbsp;و&nbsp;نام&nbsp;خانوادگی',
            'order_email' =>'ایمیل', 
            'order_amount' => 'مبلغ(ریال)', 
            'order_date' => 'تاریخ', 
            'order_referenceId' => 'شناسه مرجع',
            'order_status' =>'وضعیت', 
			'order_settle' => 'ستل', 
        );
		
        return $columns;
    }

    function get_sortable_columns() {
		
        $sortable_columns = array(
            'order_id' => array('order_id', false),
            'order_status' => array('order_status', false),
            'order_amount' => array('order_amount', false),
            'order_date' => array('order_date', false),
			'order_name_surname' => array('order_name_surname', false),
			'order_email' => array('order_email', false),
			
		
        );
        return $sortable_columns;
    }

    function get_bulk_actions(){
		echo '<style>#bulk-action-selector-top,#bulk-action-selector-bottom,#doaction,#doaction2{display:none}</style><a target="_blank" href="' . admin_url( 'admin.php?page=bank-mellat&ExportOrders=true', 'http' ) . '" class="button action">ذخیره تمامی تراکنش ها در قالب HTML</a>';
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

   
    function process_bulk_action(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'WPBEGPAY_orders'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE order_id =$ids");
            }
        }
    }

  
    function prepare_items(){
		
        global $wpdb;
        $table_name = $wpdb->prefix . 'WPBEGPAY_orders'; // do not forget about tables prefix

        $per_page = 10; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
		echo '<style type="text/css">';
		echo '.wp-list-table .column-order_id { width: 5%; }';
		echo '.wp-list-table .column-order_name_surname { width: 20%; }';
		echo '.wp-list-table .column-order_email { width: 20%; }';
		echo '.wp-list-table .column-order_date { width: 20%; }';
		echo '</style>';
        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(order_id) FROM $table_name");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'order_id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}
?>