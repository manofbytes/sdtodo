<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://manofbytes.com
 * @since      1.0.0
 *
 * @package    Sdtodo
 * @subpackage Sdtodo/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sdtodo
 * @subpackage Sdtodo/admin
 * @author     Andrei Brumusila <andrei.brumusila@gmail.com>
 */
class Sdtodo_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name    The name of this plugin.
	 * @param    string $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sdtodo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sdtodo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( get_current_screen()->id === 'dashboard' ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sdtodo-admin.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sdtodo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sdtodo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( get_current_screen()->id === 'dashboard' ) {

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sdtodo-admin.js', array( 'jquery', 'wp-i18n' ), $this->version, false );

			$sdtodo = array(
				'nonce_toggle' => wp_create_nonce( 'toggle_task' ),
				'nonce_delete' => wp_create_nonce( 'delete_task' ),
			);

			wp_localize_script( $this->plugin_name, 'sdtodo', $sdtodo );

		}
	}

	/**
	 * Setup dashboard widget
	 */
	public function define_dashboard_widget() {
		wp_add_dashboard_widget(
			$this->plugin_name,
			__( 'To Do', 'sdtodo' ),
			array( 'Sdtodo_Admin', 'get_widget_data' )
		);
	}

	/**
	 * Get widget data
	 */
	public static function get_widget_data() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'sdtodo_items';
		$todos      = $wpdb->get_results( "SELECT * FROM $table_name" ); // db call ok; no-cache ok.

		require_once plugin_dir_path( __FILE__ ) . 'partials/sdtodo-admin-display.php';
	}

	/**
	 * Add new todo
	 */
	public static function add_new_todo() {
		if ( isset( $_POST['new_todo'], $_POST['add_new_todo_nonce'] )
			&& wp_verify_nonce( sanitize_key( $_POST['add_new_todo_nonce'] ), 'add_new_todo' ) ) {

			global $wpdb;
			$table_name = $wpdb->prefix . 'sdtodo_items';

			$todo = sanitize_text_field( wp_unslash( $_POST['new_todo'] ) );

			if ( ! empty( $todo ) ) {
				$wpdb->insert(
					$table_name,
					array(
						'user_id' => get_current_user_id(),
						'todo'    => $todo,
					)
				); // db call ok; no-cache ok.

				$response = array(
					'todo'    => $todo,
					'todo_id' => $wpdb->insert_id,
				);

				wp_send_json( $response );
			} else {
				wp_send_json_error( [ 'message' => 'A todo can\'t be empty.' ] );
			}
		} else {
			wp_send_json_error( [ 'message' => 'Something went wrong.' ] );
		}
	}

	/**
	 * Complete task
	 */
	public static function toggle_task() {
		if ( isset( $_POST['task_id'], $_POST['sdtodo_toggle_task_nonce'] )
			&& is_numeric( $_POST['task_id'] )
			&& check_ajax_referer( 'toggle_task', 'sdtodo_toggle_task_nonce' ) ) {

			global $wpdb;
			$table_name  = $wpdb->prefix . 'sdtodo_items';
			$task_id     = $_POST['task_id'];
			$task_status = (int) $wpdb->get_var( $wpdb->prepare( "SELECT completed FROM {$table_name} WHERE id = %d", $task_id ) ); // db call ok; no-cache ok.

			$query = $wpdb->update(
				$table_name,
				array(
					'completed' => ! $task_status,
				),
				array(
					'id' => $task_id,
				),
				array( '%d' ),
				array( '%d' )
			); // db call ok; no-cache ok.

			$response = array(
				'todo_id' => $task_id,
			);

			wp_send_json( $response );
		} else {
			wp_send_json_error( [ 'message' => 'Something went wrong.' ] );
		}
	}

	/**
	 * Delete task
	 */
	public static function delete_task() {
		if ( isset( $_POST['sdtodo_delete_task_nonce'] )
			&& is_numeric( $_POST['task_id'] )
			&& check_ajax_referer( 'delete_task', 'sdtodo_delete_task_nonce' ) ) {

			global $wpdb;
			$table_name = $wpdb->prefix . 'sdtodo_items';
			$task_id    = $_POST['task_id'];

			$query = $wpdb->delete(
				$table_name,
				array(
					'id' => $task_id,
				),
				array( '%d' )
			); // db call ok; no-cache ok.

			$response = array(
				'todo_id' => $task_id,
			);

			wp_send_json( $response );
		} else {
			wp_send_json_error( [ 'message' => 'Something went wrong.' ] );
		}
	}
}
