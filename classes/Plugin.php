<?php
/**
 * Main plugin file.
 *
 * @package Menu_Item_Types
 */

namespace wearerequired\Custom_Menu_Item_Types;

defined( 'WPINC' ) or die;


/**
 * Menu_Item_Types class.
 */
class Plugin extends \WP_Stack_Plugin2 {

	/**
	 * Instance of this class.
	 *
	 * @var self
	 */
	protected static $instance;

	/**
	 * Plugin version.
	 */
	const VERSION = '0.1.0';

	/**
	 * Constructs the object, hooks in to `plugins_loaded`.
	 */
	protected function __construct() {
		$this->hook( 'plugins_loaded', 'add_hooks' );
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'wp_setup_nav_menu_item' ) );
		add_filter( 'walker_nav_menu_start_el', array( $this, 'nav_menu_start_el' ), 10, 4);
		add_filter( 'customize_nav_menu_available_item_types', array( $this, 'customize_nav_menu_available_item_types' ), 10, 4);
	}

	/**
	 * Adds hooks.
	 */
	public function add_hooks() {
		$this->hook( 'init' );
		$this->hook( 'admin_init' );
		$this->hook( 'admin_enqueue_scripts' );
		// Add your hooks here.
	}

	/**
	 * Initializes the plugin, registers textdomain, etc.
	 */
	public function init() {
		$this->load_textdomain( 'menu-item-types', '/languages' );
	}

	/**
	 * Add menu meta box
	 */
	public function admin_init() {
		add_meta_box(
			'r_custom_item_types',
			__( 'Custom Menu Types', 'polylang' ),
			array( $this, 'r_custom_item_types' ),
			'nav-menus',
			'side',
			'high'
		);
	}

	/**
	 * Change item label depending on the link
	 */
	public function wp_setup_nav_menu_item( $menu_item ) {
		if ( '#line_break' === $menu_item->url ) {
			$menu_item->type_label = __( 'Line Break', 'menu-item-types' );
		}
		if ( '#custom_title' === $menu_item->url ) {
			$menu_item->type_label = __( 'Title', 'menu-item-types' );
		}
		return $menu_item;
	}

	public function nav_menu_start_el( $item_output, $item, $depth, $args ){
		if(  $item->url === '#line_break' ){
			return '<hr>';
		} elseif ( $item->url === '#custom_title' ){
			return $item->post_title; // Titel ohne Link
		} else {
			return $item_output; // Normale Ausgabe für diesen Link
		}
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_style(
			'rmit-admin-style',
			$this->get_url() . 'css/menu-item-types.css',
			false,
			'1.0.0'
		);
	}

	public function customize_nav_menu_available_item_types( $item_types ) {
		// This would work if could query the custom items from somewhere.
		return $item_types;
	}

	/**
	 * Displays a metabox for the custom links menu item.
	 *
	 * @global int        $_nav_menu_placeholder
	 * @global int|string $nav_menu_selected_id
	 */
	public function r_custom_item_types() {
		global $_nav_menu_placeholder, $nav_menu_selected_id;

		$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;

		?>
		<div class="posttypediv" id="custom-item-types">
			<div id="tabs-panel-lang-switch" class="tabs-panel tabs-panel-active">
				<ul id ="lang-switch-checklist" class="categorychecklist form-no-clear">
					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php _e( 'Line Break', 'menu-item-types' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php _e( 'Line Break', 'menu-item-types' ); ?>">
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="#line_break">
					</li>
					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php _e( 'Title', 'menu-item-types' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php _e( 'Title', 'menu-item-types' ); ?>">
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="#custom_title">
					</li>
				</ul>
			</div>
			<input type="hidden" value="custom" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" />

			<p class="button-controls wp-clearfix">
				<span class="add-to-menu">
					<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'menu-item-types' ); ?>" name="add-custom-menu-item" id="submit-custom-item-types" />
					<span class="spinner"></span>
				</span>
			</p>

		</div><!-- /.custom-item-types -->
		<?php
	}
}
