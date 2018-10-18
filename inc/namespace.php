<?php

namespace Required\CustomMenuItemTypes;

/**
 * Bootstraps the plugin.
 */
function bootstrap() {
	add_action( 'init', __NAMESPACE__ . '\register_traduttore_project' );

	$custom_menu_items = new Custom_Menu_Items();
	add_action( 'admin_init', [ $custom_menu_items, 'add_meta_box' ] );
	add_filter( 'wp_setup_nav_menu_item', [ $custom_menu_items, 'customize_menu_item_label' ] );
	add_filter( 'wp_edit_nav_menu_walker', [ $custom_menu_items, 'wp_edit_nav_menu_walker' ] );
	add_filter( 'custom_menu_item_types.nav_menu_item_fields', [ $custom_menu_items, 'nav_menu_item_fields' ], 10, 2 );
	add_action( 'wp_update_nav_menu_item', [ $custom_menu_items, 'wp_update_nav_menu_item' ], 10, 3 );
	add_filter( 'walker_nav_menu_start_el', [ $custom_menu_items, 'nav_menu_start_el' ], 10, 4 );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\bootstrap' );

/**
 * Registers Traduttore project for language packs.
 *
 * @link https://translate.required.com/projects/required/custom-menu-item-types/
 *
 * @since 2.0.0
 */
function register_traduttore_project() {
	if ( ! function_exists( 'Required\Traduttore_Registry\add_project' ) ) {
		return;
	}

	\Required\Traduttore_Registry\add_project(
		'plugin',
		'custom-menu-item-types',
		'https://translate.required.com/api/translations/required/custom-menu-item-types/'
	);
}
