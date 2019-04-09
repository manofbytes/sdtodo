<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://manofbytes.com
 * @since      1.0.0
 *
 * @package    Sdtodo
 * @subpackage Sdtodo/admin/partials
 */

?>

<p class="sdtd_loading">Loading...</p>

<div class="sdtd_wrap" style="display:none;">

	<p <?php echo( $todos ? 'style="display:none;"' : '' ); ?>><?php esc_html_e( 'You don\'t have any to do items yet.', 'sdtodo' ); ?></p>

	<ul class="sdtd-items-list">
		<?php foreach ( $todos as $todo ) : ?>

			<li data-todo-id="<?php echo esc_attr( $todo->id ); ?>">

				<span class="sdtd-item-status incomplete" <?php echo( $todo->completed ? 'style="display:none"' : '' ); ?>></span>

				<span class="sdtd-item-status complete" <?php echo( $todo->completed ? 'style="display:block"' : '' ); ?>>
					<span class="dashicons dashicons-yes"></span>
				</span>

				<?php echo '<span class="sdtd-todo-body">' . esc_html( $todo->todo ) . '</span>'; ?>

				<span class="sdtd-item-delete dashicons dashicons-trash"></span>
			</li>

		<?php endforeach; ?>
	</ul>

	<hr>

	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="sdtd_add_new">

		<div class="sdtodo_error" style="display:block;"></div>

		<div class="input-text-wrap" id="new-todo">
			<label class="prompt screen-reader-text" for="new_todo" ><?php esc_html_e( 'New to do' ); ?></label>
			<input  type="text" name="new_todo" autocomplete="off">
		</div>

		<p class="submit">
			<?php wp_nonce_field( 'add_new_todo', 'add_new_todo_nonce' ); ?>
			<input type="hidden" name="action" value="add_new_todo">
			<input type="submit" name="save" id="save-post" class="button button-primary" value="<?php esc_html_e( 'Add To Do', 'sdtodo' ); ?>">
			<br class="clear">
		</p>

	</form>

</div>
