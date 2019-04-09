(function( $ ) {
	'use strict';
	const { __ } = wp.i18n;

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	$( window ).load(function() {

		$('.sdtd_loading').hide();
		$('.sdtd_wrap').show();

		if( $('#new-todo input').val().length < 1 ) {
			$('#new-todo label').removeClass('screen-reader-text');
		}

		$('#new-todo').on( 'click', function(){
			if( ! $('label', this).hasClass('screen-reader-text') ) {
				$('label', this).addClass('screen-reader-text');
				$('input', this).focus();
			};
		} );

		$('#new-todo input').on( 'focusout', function(){
			if( $(this).siblings('label').hasClass('screen-reader-text') && $(this).val().length < 1 ) {
				$(this).siblings('label').removeClass('screen-reader-text');
			};
		} );

		/**
		 * Add new to do item
		 */
		$('#sdtd_add_new').submit( function(e){
			e.preventDefault();

			var form_data = $('#sdtd_add_new').serialize();

			$.ajax({
				url: ajaxurl,
				type: 'post',
				data: form_data
			}).done( function(response){

				if( typeof response.success == 'undefined' ) {

					var todo_html = '<li ' + 'data-todo-id="' + response.todo_id + '"><span class="sdtd-item-status incomplete"></span><span class="sdtd-item-status complete"><span class="dashicons dashicons-yes"></span></span><span class="sdtd-todo-body">' + response.todo + '</span><span class="sdtd-item-delete dashicons dashicons-trash"></span></li>';

					if( $('#sdtodo .sdtd_wrap > p').is(":visible") ) {
						$('#sdtodo .sdtd_wrap > p').hide();
					}

					$('#sdtodo').find('ul').append(todo_html);
					$('#sdtodo input[type="text"]').val('').blur();

				} else {

					$('.sdtodo_error').text( __(response.data.message) ).show();

					setInterval(function() {
						$('.sdtodo_error').hide();
					}, 5000);

				}

			}).fail( function(){

				$('.sdtodo_error').text( __('Something went wrong', 'sdtodo') ).show();

				setInterval(function() {
					$('.sdtodo_error').hide();
				}, 5000);

			}).always( function() {
				e.target.reset();
			});
		});

		/**
		 * Complete / Uncomplete items
		 */
		$('#sdtodo').on( 'click', '.sdtd-item-status', function(e){
			var element = $(this);
			var task_id = element.parent('li').data('todo-id');

			$.ajax({
				url: ajaxurl,
				type: 'post',
				data: {
					task_id: task_id,
					sdtodo_toggle_task_nonce: sdtodo.nonce_toggle,
					action: 'toggle_task',
					value: true
				}
			}).done( function(){
				element.fadeOut('fast', function(){
					element.siblings('.sdtd-item-status').fadeIn();
				});
			}).fail( function(){

				$('.sdtodo_error').text( __('Something went wrong', 'sdtodo') ).show();

				setInterval(function() {
					$('.sdtodo_error').hide();
				}, 5000);

			});
		});

		/**
		 * Delete task
		 */
		$('#sdtodo').on( 'click', '.sdtd-item-delete', function(e){
			var element = $(this);
			var list = element.parents('ul');
			var task_id = element.parent('li').data('todo-id');

			$.ajax({
				url: ajaxurl,
				type: 'post',
				data: {
					task_id: task_id,
					sdtodo_delete_task_nonce: sdtodo.nonce_delete,
					action: 'delete_task',
					value: true
				}
			}).done( function(){

				element.parent('li').hide();
				element.parent('li').remove();

				if( ! list.children().length ) {
					list.siblings('p').show();
				}

			}).fail( function(){

				$('.sdtodo_error').text( __('Something went wrong', 'sdtodo') ).show();

				setInterval(function() {
					$('.sdtodo_error').hide();
				}, 5000);

			});
		});

	});

})( jQuery );
