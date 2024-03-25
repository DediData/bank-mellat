<?php
/**
 * Bank Mellat Orders List Class
 * 
 * @package Bank_Mellat
 */

declare(strict_types=1);

namespace BankMellat;

/**
 * Class Bank_Mellat_Orders_List
 */
final class Bank_Mellat_Orders_List extends WP_List_Table {

	/**
	 * Constructor
	 * 
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function __construct() {
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$status = $GLOBALS['status'];
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$page            = $GLOBALS['page'];
		$construct_array = array(
			'singular' => 'person',
			'plural'   => 'persons',
		);

		parent::__construct( $construct_array );
	}

	/**
	 * Column Default
	 * 
	 * @param array<mixed> $item        Item.
	 * @param mixed        $column_name Column Name.
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Column Order Name Surname
	 * 
	 * @param array<mixed> $item Item.
	 * @return mixed
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function column_order_name_surname( $item ) {
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$wpdb      = $GLOBALS['wpdb'];
		$order_id  = $item['order_id'];
		$tablename = $wpdb->prefix . 'WPBEGPAY_orders';

		$get_order = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %s WHERE order_id = %d', $tablename, $order_id ) );
		if ( $get_order ) {
			$actions = array(
				'view'   => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=bank-mellat&orderId=' . $item['order_id'], 'admin' ), 'نمایش جزئیات' ),
				'delete' => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=bank-mellat&action=delete&id=' . $item['order_id'], 'admin' ), 'حذف' ),
			);
		}
		return sprintf(
			'%s %s',
			$item['order_name_surname'],
			$this->row_actions( $actions )
		);
	}

	/**
	 * Column Order Amount
	 * 
	 * @param array<mixed> $item Item.
	 * @return mixed
	 */
	public function column_order_amount( $item ) {
		return '<em>' . number_format( $item['order_amount'] ) . '</em>';
	}

	/**
	 * Column Order Status
	 * 
	 * @param array<mixed> $item Item.
	 * @return mixed
	 */
	public function column_order_status( $item ) {
		return 'yes' === $item['order_status'] ? 'انجام شده است' : 'انجام نشده است';
	}

	/**
	 * Column Order Settle
	 * 
	 * @param array<mixed> $item Item.
	 * @return mixed
	 */
	public function column_order_settle( $item ) {
		return 'yes' === $item['order_settle'] ? 'انجام شده است' : 'انجام نشده است';
	}

	/**
	 * Get Columns
	 * 
	 * @return mixed
	 */
	public function get_columns() {
		return array(
			'order_id'           => '#',
			'order_name_surname' => 'نام و نام خانوادگی',
			'order_email'        => 'ایمیل',
			'order_amount'       => 'مبلغ (ریال)',
			'order_date'         => 'تاریخ',
			'order_referenceId'  => 'شناسه مرجع',
			'order_status'       => 'وضعیت',
			'order_settle'       => 'ستل',
		);
	}

	/**
	 * Get Sortable Columns
	 * 
	 * @return mixed
	 */
	public function get_sortable_columns() {
		return array(
			'order_id'           => array( 'order_id', false ),
			'order_status'       => array( 'order_status', false ),
			'order_amount'       => array( 'order_amount', false ),
			'order_date'         => array( 'order_date', false ),
			'order_name_surname' => array( 'order_name_surname', false ),
			'order_email'        => array( 'order_email', false ),
		);
	}

	/**
	 * Get Bulk Actions
	 * 
	 * @return mixed
	 */
	public function get_bulk_actions() {
		echo '
			<style>
				#bulk-action-selector-top,
				#bulk-action-selector-bottom,
				#doaction,
				#doaction2 {
					display:none
				}
			</style>
			<a target="_blank" href="' . esc_url( admin_url( 'admin.php?page=bank-mellat&ExportOrders=true', 'admin' ) ) . '" class="button action">ذخیره تمامی تراکنش ها در قالب HTML</a>
		';
		return array( 'delete' => 'Delete' );
	}

	/**
	 * Process Bulk Actions
	 * 
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function process_bulk_action() {
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$wpdb       = $GLOBALS['wpdb'];
		$table_name = $wpdb->prefix . 'WPBEGPAY_orders';

		if ( 'delete' !== $this->current_action() ) {
			return;
		}
		$get_id = filter_input( \INPUT_GET, 'id', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$ids    = $get_id ?? array();
		if ( is_array( $ids ) ) {
			$ids = implode( ',', $ids );
		}
		if ( null === $ids ) {
			return;
		}
		$wpdb->query( $wpdb->prepare( 'DELETE FROM %s WHERE order_id = %d', $table_name, $ids ) );
	}

	/**
	 * Prepare Items
	 * 
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function prepare_items() {
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$wpdb       = $GLOBALS['wpdb'];
		$table_name = $wpdb->prefix . 'WPBEGPAY_orders';
		$per_page   = 10;
		$columns    = $this->get_columns();
		$hidden     = array();
		$sortable   = $this->get_sortable_columns();
		echo '
			<style type="text/css">
				.wp-list-table .column-order_id { width: 5%; }
				.wp-list-table .column-order_name_surname { width: 20%; }
				.wp-list-table .column-order_email { width: 20%; }
				.wp-list-table .column-order_date { width: 20%; }
			</style>
		';
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