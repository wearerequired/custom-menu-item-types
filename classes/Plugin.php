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
		//add_action( 'init', array( $post_type, 'register' ) );
	}

	protected function register_backend() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'wp_setup_nav_menu_item' ) );
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'wp_edit_nav_menu_walker' ), 10, 2 );
		add_filter( 'wp_nav_menu_item_fields', array( $this, 'wp_nav_menu_item_fields' ), 10, 2 );
		add_action( 'wp_update_nav_menu_item', array( $this, 'wp_update_nav_menu_item'), 10, 2 );
		//add_filter( 'customize_nav_menu_available_item_types', array( $this, 'customize_nav_menu_available_item_types' ), 10, 4);
	}

	protected function register_frontend() {
		add_filter( 'walker_nav_menu_start_el', array( $this, 'nav_menu_start_el' ), 10, 4);
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
	 * Load the plugin textdomain.
	 *
	 * @return bool Returns true if the textdomain was loaded successfully, false otherwise.
	 */
	public function load_textdomain() {
		return load_plugin_textdomain( 'menu-item-types', false, basename( dirname( RCMIT_FILE ) ) . 'languages' );
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

	public function admin_enqueue_scripts( $hook ) {
		if ( $hook !== 'nav-menus.php' ){
			return;
		}
		wp_enqueue_style(
			'rmit-admin-style',
			$this->get_url() . 'css/menu-item-types.css',
			false,
			'1.0.0'
		);
		wp_enqueue_script(
			'rmit-menu-script',
			$this->get_url() . 'js/src/menu-item-types.js',
			false,
			'1.0.0'
		);
		$translation_array = array(
			'line_break_title' => __( 'Line Break', 'menu-item-types' ),
		);
		wp_localize_script(
			'rmit-menu-script',
			'rcmit_data',
			$translation_array
		);
	}

	/**
	 * Change item label depending on the link
	 */
	public function wp_setup_nav_menu_item( $menu_item ) {
		if ( 'custom' !== $menu_item->type ) {
			return $menu_item;
		}
		switch ( $menu_item->url ) {
			case '#line_break':
				$menu_item->type_label = __( 'Line Break', 'menu-item-types' );
				break;
			case '#custom_headline':
				$menu_item->type_label = __( 'Headline', 'menu-item-types' );
				break;
			case '#pll_switcher':
				$menu_item->type_label = __( 'Language Switcher', 'menu-item-types' );
				break;
		}
		$menu_item->rcmit_type = ! isset( $menu_item->rcmit_type ) ? get_post_meta( $menu_item->ID, '_menu_item_rcmit_type', true ) : $menu_item->rcmit_type;
		$menu_item->rcmit_header = ! isset( $menu_item->rcmit_header ) ? get_post_meta( $menu_item->ID, '_menu_item_rcmit_header', true ) : $menu_item->rcmit_header;
		switch ( $menu_item->rcmit_type ) {
			case 'highlight_box':
				$menu_item->type_label = __( 'Highlight Box', 'menu-item-types' );
				break;
			case 'newsletter_box':
				$menu_item->type_label = __( 'Newsletter Box', 'menu-item-types' );
				break;
		}
		return $menu_item;
	}

	public function nav_menu_start_el( $item_output, $item, $depth, $args ){
		if ( 'custom' !== $item->type ) {
			return $item_output;
		}
		switch ( $item->url ) {
			case '#line_break':
				$item_output = '<hr>';
				break;
			case '#custom_headline':
				$item_output = '<h3>' . $item->post_title . '</h3>';
				break;
		}
		switch ( $item->rcmit_type ) {
			case 'highlight_box':
				$item_output = $item_output;
				break;
			case 'newsletter_box':
				$item_output = $item_output;
				break;
		}
		return $item_output;
	}

	public function wp_edit_nav_menu_walker( $class, $menu_id ) {
		return 'required\Custom_Menu_Item_Types\Walker_Custom_Item_Types';
	}

	public function wp_nav_menu_item_fields( $nav_menu_item_fields, $context ) {
		if ( 'custom' !== $context['item']->type ) {
			return $nav_menu_item_fields;
		}
		if ( isset( $context['item']->rcmit_type ) ) { ?>
			<input class="menu-item-data-rcmit-type" type="hidden" name="menu-item-rcmit-type[<?php echo $context['item']->ID; ?>]" value="<?php echo $context['item']->rcmit_type; ?>" />
			<?php
			$new_nav_menu_item_fields = array();
			if ( 'highlight_box' === $context['item']->rcmit_type ) {
				unset( $nav_menu_item_fields['title'] );
				unset( $nav_menu_item_fields['custom'] );
				ob_start(); ?>
				<p class="field-title description description-wide">
					<label for="edit-menu-item-title-<?php echo $context['item']->ID; ?>">
						<?php _e( 'Button Text' ); ?><br />
						<input type="text" id="edit-menu-item-title-<?php echo $context['item']->ID; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $context['item']->ID; ?>]" value="<?php echo esc_attr( $context['item']->title ); ?>" />
					</label>
				</p>
				<?php $new_nav_menu_item_fields['title'] = ob_get_clean(); ?>
				<?php ob_start(); ?>
				<p class="field-url description description-wide">
					<label for="edit-menu-item-url-<?php echo $context['item']->ID; ?>">
						<?php _e( 'Button URL', 'menu-item-types' ); ?><br />
						<input type="text" id="edit-menu-item-url-<?php echo $context['item']->ID; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $context['item']->ID; ?>]" value="<?php echo esc_attr( $context['item']->url ); ?>" />
					</label>
				</p>
				<?php $new_nav_menu_item_fields['highlight_box'] = ob_get_clean(); ?>
				<?php ob_start(); ?>
				<p class="field-header description description-wide">
					<label for="edit-menu-item-header-<?php echo $context['item']->ID; ?>">
						<?php _e( 'Box Header', 'menu-item-types' ); ?><br />
						<input type="text" id="edit-menu-item-header-<?php echo $context['item']->ID; ?>" class="widefat code edit-menu-item-header" name="menu-item-header[<?php echo $context['item']->ID; ?>]" value="<?php echo esc_attr( $context['item']->rcmit_header ); ?>" />
					</label>
				</p>
				<?php $new_nav_menu_item_fields['highlight_box_header'] = ob_get_clean();
			}
			if ( 'newsletter_box' === $context['item']->rcmit_type ) {
				unset( $nav_menu_item_fields['title'] );
				unset( $nav_menu_item_fields['custom'] );
				ob_start(); ?>
				<p class="field-title description description-wide">
					<label for="edit-menu-item-title-<?php echo $context['item']->ID; ?>">
						<?php _e( 'Header' ); ?><br />
						<input type="text" id="edit-menu-item-title-<?php echo $context['item']->ID; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $context['item']->ID; ?>]" value="<?php echo esc_attr( $context['item']->title ); ?>" />
					</label>
				</p>
				<?php $new_nav_menu_item_fields['title'] = ob_get_clean(); ?>
				<?php ob_start(); ?>
				<p class="field-shortcode description description-wide">
					<label for="edit-menu-item-header-<?php echo $context['item']->ID; ?>">
						<?php _e( 'Shortcode', 'menu-item-types' ); ?><br />
						<input type="text" id="edit-menu-item-shortcode-<?php echo $context['item']->ID; ?>" class="widefat code edit-menu-item-shortcode" name="menu-item-shortcode[<?php echo $context['item']->ID; ?>]" value="<?php echo esc_attr( $context['item']->rcmit_shortcode ); ?>" />
					</label>
				</p>
				<?php $new_nav_menu_item_fields['newsletter_box_shortcode'] = ob_get_clean();
			}
			$nav_menu_item_fields = array_merge( $new_nav_menu_item_fields, $nav_menu_item_fields );
		}
		return $nav_menu_item_fields;
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
			<div id="tabs-panel-custom-item-types" class="tabs-panel tabs-panel-active">
				<ul id ="custom-item-types-checklist" class="categorychecklist form-no-clear">
					<li>
						<label class="menu-item-title">
							<input type="radio" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php _e( 'Line Break', 'menu-item-types' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php _e( 'Line Break', 'menu-item-types' ); ?>">
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="#line_break">
					</li>
					<li>
						<label class="menu-item-title">
							<input type="radio" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php _e( 'Headline', 'menu-item-types' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php _e( 'Headline', 'menu-item-types' ); ?>">
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="#custom_headline">
					</li>
					<li>
						<label class="menu-item-title">
							<input type="radio" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php _e( 'Highlight Box', 'menu-item-types' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-rcmit-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-rcmit-type]" value="highlight_box">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php _e( 'Highlight Box', 'menu-item-types' ); ?>">
					</li>
					<li>
						<label class="menu-item-title">
							<input type="radio" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php _e( 'Newsletter Box', 'menu-item-types' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-rcmit-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-rcmit-type]" value="newsletter_box">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php _e( 'Newsletter Box', 'menu-item-types' ); ?>">
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

	public function wp_update_nav_menu_item( $menu_id = 0, $menu_item_db_id = 0 ) {
		if ( empty( $_POST['menu-item-rcmit-type'][ $menu_item_db_id ] ) && empty( $_POST['menu-item-header'][ $menu_item_db_id ] ) && empty( $_POST['menu-item-shortcode'][ $menu_item_db_id ] ) ) {
			return;
		}
		// security check
		// as 'wp_update_nav_menu_item' can be called from outside WP admin
		// && wp_verify_nonce( 'update-nav_menu', 'update-nav-menu-nonce' )
		if ( current_user_can( 'edit_theme_options' ) ) {
			update_post_meta(
				$menu_item_db_id,
				'_menu_item_rcmit_type',
				sanitize_text_field( $_POST['menu-item-rcmit-type'][ $menu_item_db_id ] )
			);
			update_post_meta(
				$menu_item_db_id,
				'_menu_item_rcmit_header',
				sanitize_text_field( $_POST['menu-item-header'][ $menu_item_db_id ] )
			);
			update_post_meta(
				$menu_item_db_id,
				'_menu_item_rcmit_shortcode',
				sanitize_text_field( $_POST['menu-item-shortcode'][ $menu_item_db_id ] )
			);
		}
	}

}
