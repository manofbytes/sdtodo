<?php
/**
 * Fired during plugin activation
 *
 * @link       https://manofbytes.com
 * @since      1.0.0
 *
 * @package    Sdtodo
 * @subpackage Sdtodo/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Sdtodo
 * @subpackage Sdtodo/includes
 * @author     Andrei Brumusila <andrei.brumusila@gmail.com>
 */
class Sdtodo_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		$db_version = '1.0';

		global $wpdb;

		$table_name = $wpdb->prefix . 'sdtodo_items';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id int(11) unsigned NOT NULL AUTO_INCREMENT,
			user_id int(11) unsigned NOT NULL,
			todo text NOT NULL,
			completed boolean DEFAULT 0 NOT NULL,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $sql );

		add_option( ( new Sdtodo() )->get_plugin_name() . '_db_version', $db_version );

	}

}
