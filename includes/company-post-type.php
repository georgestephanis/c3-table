<?php
/**
 * The Companies Post Type.
 *
 * This was originally created in ACF, and exported here for consistency and reliability.
 */

add_action( 'init', function() {
	register_post_type( 'company', array(
	'labels' => array(
		'name' => 'Companies',
		'singular_name' => 'Company',
		'menu_name' => 'Companies',
		'all_items' => 'All Companies',
		'edit_item' => 'Edit Company',
		'view_item' => 'View Company',
		'view_items' => 'View Companies',
		'add_new_item' => 'Add New Company',
		'add_new' => 'Add New Company',
		'new_item' => 'New Company',
		'parent_item_colon' => 'Parent Company:',
		'search_items' => 'Search Companies',
		'not_found' => 'No companies found',
		'not_found_in_trash' => 'No companies found in Trash',
		'archives' => 'Company Archives',
		'attributes' => 'Company Attributes',
		'insert_into_item' => 'Insert into company',
		'uploaded_to_this_item' => 'Uploaded to this company',
		'filter_items_list' => 'Filter companies list',
		'filter_by_date' => 'Filter companies by date',
		'items_list_navigation' => 'Companies list navigation',
		'items_list' => 'Companies list',
		'item_published' => 'Company published.',
		'item_published_privately' => 'Company published privately.',
		'item_reverted_to_draft' => 'Company reverted to draft.',
		'item_scheduled' => 'Company scheduled.',
		'item_updated' => 'Company updated.',
		'item_link' => 'Company Link',
		'item_link_description' => 'A link to a company.',
	),
	'description' => 'Companies to be used in comparison tables.',
	'public' => true,
	'exclude_from_search' => true,
	'publicly_queryable' => false,
	'show_in_nav_menus' => false,
	'show_in_admin_bar' => false,
	'show_in_rest' => true,
	'menu_icon' => 'dashicons-building',
	'supports' => array(
		0 => 'title',
	),
	'delete_with_user' => false,
) );
} );

add_filter( 'enter_title_here', function( $default, $post ) {
	switch ( $post->post_type ) {
		case 'company':
			return 'Company Title';
	}

	return $default;
}, 10, 2 );

