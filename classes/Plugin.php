<?php
/**
 * Main plugin file.
 *
 * @package Menu_Item_Types
 */

namespace required\Custom_Menu_Item_Types;

defined( 'WPINC' ) or die;

/**
 * Menu_Item_Types class.
 */
class Plugin {

	public function run() {
		$this->register_common();
		if ( is_admin() ) {
			$this->register_backend();
		} else {
			$this->register_frontend();
		}
	}

	protected function register_common() {
		add_action( 'init', [ $this, 'register_traduttore_project' ] );
	}

	protected function register_backend() {
		$custom_menu_items = new Custom_Menu_Items;
		add_action( 'admin_init', array( $custom_menu_items, 'add_meta_box' ) );
		add_filter( 'wp_setup_nav_menu_item', array( $custom_menu_items, 'customize_menu_item_label' ) );
		add_filter( 'wp_edit_nav_menu_walker', array( $custom_menu_items, 'wp_edit_nav_menu_walker' ), 10, 2 );
		add_filter( 'wp_nav_menu_item_fields', array( $custom_menu_items, 'wp_nav_menu_item_fields' ), 10, 2 );
		add_action( 'wp_update_nav_menu_item', array( $custom_menu_items, 'wp_update_nav_menu_item'), 10, 3 );
		//add_filter( 'customize_nav_menu_available_item_types', array( $this, 'customize_nav_menu_available_item_types' ), 10, 4);
	}

	protected function register_frontend() {
		$custom_menu_items = new Custom_Menu_Items;
		add_filter( 'walker_nav_menu_start_el', array( $custom_menu_items, 'nav_menu_start_el' ), 10, 4);
	}

	/**
	 * Returns the URL to the plugin directory
	 *
	 * @return string The URL to the plugin directory.
	 */
	public function get_url() {
		return plugin_dir_url( RCMIT_FILE );
	}

	/**
	 * Returns the path to the plugin directory.
	 *
	 * @return string The absolute path to the plugin directory.
	 */
	public function get_path() {
		return plugin_dir_path( RCMIT_FILE );
	}

	/**
	 * Registers Traduttore project for language packs.
	 *
	 * @link https://translate.required.com/projects/required/custom-menu-item-types/
	 *
	 * @since 2.0.0
	 */
	public function register_traduttore_project() {
		if ( ! function_exists( 'Required\Traduttore_Registry\add_project' ) ) {
			return;
		}

		\Required\Traduttore_Registry\add_project(
			'plugin',
			'custom-menu-item-types',
			'https://translate.required.com/api/translations/required/custom-menu-item-types/'
		);
	}
}
