<?php
/**
 * Main plugin file.
 *
 * @package Menu_Item_Types
 */

namespace Required\CustomMenuItemTypes;

use Required\CustomMenuItemTypes\Walker\NavMenuEditWithCustomItemTypes;

/**
 * Menu_Item_Types class.
 */
class Custom_Menu_Items {

	/**
	 * Add menu meta box
	 */
	public function add_meta_box() {
		add_meta_box(
			'r_custom_item_types',
			__( 'Custom Menu Item Types', 'custom-menu-item-types' ),
			array( $this, 'r_custom_item_types' ),
			'nav-menus',
			'side',
			'high'
		);
	}

	/**
	 * Change item label depending on the link
	 */
	public function customize_menu_item_label( $menu_item ) {
		if ( 'custom' !== $menu_item->type ) {
			return $menu_item;
		}

		switch ( $menu_item->url ) {
			case '#line_break':
				$menu_item->type_label = __( 'Separator', 'custom-menu-item-types' );
				break;
			case '#column_end':
				$menu_item->type_label = __( 'Column End', 'custom-menu-item-types' );
				break;
			case '#custom_headline':
				$menu_item->type_label = __( 'Headline', 'custom-menu-item-types' );
				break;
		}

		$menu_item->rcmit_type          = $menu_item->rcmit_type ?? get_post_meta( $menu_item->ID, '_menu_item_rcmit_type', true );
		$menu_item->rcmit_button_text   = $menu_item->rcmit_button_text ?? get_post_meta( $menu_item->ID, '_menu_item_rcmit_button_text', true );
		$menu_item->rcmit_shortcode     = $menu_item->rcmit_shortcode ?? get_post_meta( $menu_item->ID, '_menu_item_rcmit_shortcode', true );
		$menu_item->rcmit_column        = $menu_item->rcmit_column ?? get_post_meta( $menu_item->ID, '_menu_item_rcmit_column', true );
		$menu_item->rcmit_heading_level = $menu_item->rcmit_heading_level ?? get_post_meta( $menu_item->ID, '_menu_item_rcmit_heading_level', true );

		switch ( $menu_item->rcmit_type ) {
			case 'highlight_box':
				$menu_item->type_label = __( 'Highlight Box', 'custom-menu-item-types' );
				break;
			case 'newsletter_box':
			case 'shortcode_box':
				$menu_item->type_label = __( 'Shortcode Box', 'custom-menu-item-types' );
				break;
		}

		return $menu_item;
	}

	public function nav_menu_start_el( $item_output, $item, $depth, $args ){
		if ( 'custom' !== $item->type ) {
			return $item_output;
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );
		/** This filter is documented in wp-includes\nav-menu-template.php */
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item->rcmit_type        = $item->rcmit_type ?? get_post_meta( $item->ID, '_menu_item_rcmit_type', true );
		$item->rcmit_button_text = $item->rcmit_button_text ?? get_post_meta( $item->ID, '_menu_item_rcmit_button_text', true );
		$item->rcmit_shortcode   = $item->rcmit_shortcode ?? get_post_meta( $item->ID, '_menu_item_rcmit_shortcode', true );

		switch ( $item->url ) {
			case '#line_break':
				$item_output = '<hr>';
				break;
			case '#column_end':
				$item_output = '';
				break;
			case '#custom_headline':
				$heading_level = $item->rcmit_heading_level ?: '4';
				$item_output   = "<h{$heading_level}>{$item->post_title}</h{$heading_level}>";
				break;
		}

		switch ( $item->rcmit_type ) {
			case 'highlight_box':
				$item_output  = $args->before;
				$item_output .= '<h4>' . $title . '</h4>';
				$item_output .= '<p>' . esc_html( $item->description ) . '</p>';
				$item_output .= '<a class="button" href="' . esc_url( $item->url ) . '">';
				$item_output .= $args->link_before . esc_html( $item->rcmit_button_text ) . $args->link_after;
				$item_output .= '</a>';
				$item_output .= $args->after;
				break;
			case 'newsletter_box':
			case 'shortcode_box':
				$item_output = $args->before . '<div><h4>' . esc_html( $title ) . '</h4><p>' . esc_html( $item->description ) . '</p>' . do_shortcode( $item->rcmit_shortcode ) . '</div>' . $args->after;
				break;
		}

		return $item_output;
	}

	public function wp_edit_nav_menu_walker() {
		return NavMenuEditWithCustomItemTypes::class;
	}

	/**
	 * Filters list of settings fields of a menu item.
	 *
	 * @param array $nav_menu_item_fields Mapping of ID to the field paragraph HTML.
	 * @param array $context {
	 *     Context for applied filter.
	 *
	 *     @type \Walker_Nav_Menu_Edit $walker Nav menu walker.
	 *     @type object                $item   Menu item data object.
	 *     @type int                   $depth  Current depth.
	 * }
	 * @return array Mapping of ID to the field paragraph HTML.
	 */
	public function nav_menu_item_fields( $nav_menu_item_fields, $context ) {
		if ( 'custom' !== $context['item']->type ) {
			return $nav_menu_item_fields;
		}

		switch ( $context['item']->url ) {
			case '#column_end':
				ob_start();
				?>
					<p class="field-column description description-wide">
						<label for="edit-menu-item-column-<?php echo $context['item']->ID; ?>">
							<?php _e( 'Width of next column', 'custom-menu-item-types' ); ?><br />
							<select name="menu-item-column[<?php echo $context['item']->ID; ?>]">
								<option value="col-3" <?php selected( $context['item']->rcmit_column, 'col-3' ) ?>><?php _e( 'Col 3', 'custom-menu-item-types' ); ?></option>
								<option value="col-4" <?php selected( $context['item']->rcmit_column, 'col-4' ) ?>><?php _e( 'Col 4', 'custom-menu-item-types' ); ?></option>
								<option value="col-6" <?php selected( $context['item']->rcmit_column, 'col-6' ) ?>><?php _e( 'Col 6', 'custom-menu-item-types' ); ?></option>
							</select>
						</label>
					</p>
				<?php
				$nav_menu_item_fields['column_width'] = ob_get_clean();

			case '#line_break':
				unset( $nav_menu_item_fields['css-classes'] );

				ob_start();
				?>
					<input type="hidden" id="edit-menu-item-title-<?php echo $context['item']->ID; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $context['item']->ID; ?>]" value="<?php echo esc_attr( $context['item']->title ); ?>" />
				<?php
				$nav_menu_item_fields['title'] = ob_get_clean();

			case '#custom_headline':
				unset(
					$nav_menu_item_fields['attr-title'],
					$nav_menu_item_fields['link-target'],
					$nav_menu_item_fields['xfn'],
					$nav_menu_item_fields['description']
				);

				ob_start();
				?>
					<input type="hidden" id="edit-menu-item-url-<?php echo $context['item']->ID; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $context['item']->ID; ?>]" value="<?php echo esc_attr( $context['item']->url ); ?>" />
				<?php
				$nav_menu_item_fields['custom'] = ob_get_clean();

				ob_start();
				?>
				<p class="field-heading-level description description-wide">
					<label for="edit-menu-item-heading-level-<?php echo $context['item']->ID; ?>">
						<?php _e( 'Heading level', 'custom-menu-item-types' ); ?><br />
						<select name="menu-item-heading-level[<?php echo $context['item']->ID; ?>]">
							<?php $heading_level = $context['item']->rcmit_heading_level ?: '4'; ?>
							<option value="2" <?php selected( $heading_level, '2' ) ?>><?php _e( 'H2', 'custom-menu-item-types' ); ?></option>
							<option value="3" <?php selected( $heading_level, '3' ) ?>><?php _e( 'H3', 'custom-menu-item-types' ); ?></option>
							<option value="4" <?php selected( $heading_level, '4' ) ?>><?php _e( 'H4', 'custom-menu-item-types' ); ?></option>
							<option value="5" <?php selected( $heading_level, '5' ) ?>><?php _e( 'H5', 'custom-menu-item-types' ); ?></option>
							<option value="6" <?php selected( $heading_level, '6' ) ?>><?php _e( 'H6', 'custom-menu-item-types' ); ?></option>
						</select>
					</label>
				</p>
				<?php
				$nav_menu_item_fields['heading_level'] = ob_get_clean();

		}

		if ( ! empty( $context['item']->rcmit_type ) ) {
			?>
			<input class="menu-item-data-rcmit-type" type="hidden" name="menu-item-rcmit-type[<?php echo $context['item']->ID; ?>]" value="<?php echo $context['item']->rcmit_type; ?>" />
			<?php
			unset(
				$nav_menu_item_fields['title'],
				$nav_menu_item_fields['custom'],
				$nav_menu_item_fields['attr-title'],
				$nav_menu_item_fields['link-target'],
				$nav_menu_item_fields['xfn']
			);

			$new_nav_menu_item_fields = array();
			if ( 'highlight_box' === $context['item']->rcmit_type ) {
				ob_start();
				?>
				<p class="field-title description description-wide">
					<label for="edit-menu-item-title-<?php echo $context['item']->ID; ?>">
						<?php _e( 'Box Header', 'custom-menu-item-types' ); ?><br />
						<input type="text" id="edit-menu-item-title-<?php echo $context['item']->ID; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $context['item']->ID; ?>]" value="<?php echo esc_attr( $context['item']->title ); ?>" />
					</label>
				</p>
				<?php
				$new_nav_menu_item_fields['title'] = ob_get_clean();

				ob_start();
				?>
				<p class="field-button-text description description-wide">
					<label for="edit-menu-item-button-text-<?php echo $context['item']->ID; ?>">
						<?php _e( 'Button Text', 'custom-menu-item-types' ); ?><br />
						<input type="text" id="edit-menu-item-button-text-<?php echo $context['item']->ID; ?>" class="widefat code edit-menu-item-button-text" name="menu-item-button-text[<?php echo $context['item']->ID; ?>]" value="<?php echo esc_attr( $context['item']->rcmit_button_text ); ?>" />
					</label>
				</p>
				<?php
				$new_nav_menu_item_fields['button_text'] = ob_get_clean();

				ob_start();
				?>
				<p class="field-url description description-wide">
					<label for="edit-menu-item-url-<?php echo $context['item']->ID; ?>">
						<?php _e( 'Button URL', 'custom-menu-item-types' ); ?><br />
						<input type="text" id="edit-menu-item-url-<?php echo $context['item']->ID; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $context['item']->ID; ?>]" value="<?php echo esc_attr( $context['item']->url ); ?>" />
					</label>
				</p>
				<?php
				$new_nav_menu_item_fields['highlight_box'] = ob_get_clean();

				$new_nav_menu_item_fields['description'] = $nav_menu_item_fields['description'];
			}

			if ( 'shortcode_box' === $context['item']->rcmit_type || 'newsletter_box' === $context['item']->rcmit_type ) {
				ob_start();
				?>
				<p class="field-title description description-wide">
					<label for="edit-menu-item-title-<?php echo $context['item']->ID; ?>">
						<?php _e( 'Header', 'custom-menu-item-types' ); ?><br />
						<input type="text" id="edit-menu-item-title-<?php echo $context['item']->ID; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $context['item']->ID; ?>]" value="<?php echo esc_attr( $context['item']->title ); ?>" />
					</label>
				</p>
				<?php
				$new_nav_menu_item_fields['title'] = ob_get_clean();

				$new_nav_menu_item_fields['description'] = $nav_menu_item_fields['description'];

				ob_start();
				?>
				<p class="field-shortcode description description-wide">
					<label for="edit-menu-item-shortcode-<?php echo $context['item']->ID; ?>">
						<?php _e( 'Shortcode Box', 'custom-menu-item-types' ); ?><br />
						<input type="text" id="edit-menu-item-shortcode-<?php echo $context['item']->ID; ?>" class="widefat code edit-menu-item-shortcode" name="menu-item-shortcode[<?php echo $context['item']->ID; ?>]" value="<?php echo esc_attr( $context['item']->rcmit_shortcode ); ?>" />
					</label>
				</p>
				<?php
				$new_nav_menu_item_fields['shortcode_box_shortcode'] = ob_get_clean();
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
							<input type="radio" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php _e( 'Column End', 'custom-menu-item-types' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php _e( 'Column End', 'custom-menu-item-types' ); ?>">
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="#column_end">
					</li>
					<li>
						<label class="menu-item-title">
							<input type="radio" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php _e( 'Line Break', 'custom-menu-item-types' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php _e( 'Separator', 'custom-menu-item-types' ); ?>">
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="#line_break">
					</li>
					<li>
						<label class="menu-item-title">
							<input type="radio" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php _e( 'Headline', 'custom-menu-item-types' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php _e( 'Headline', 'custom-menu-item-types' ); ?>">
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="#custom_headline">
					</li>
					<li>
						<label class="menu-item-title">
							<input type="radio" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php _e( 'Highlight Box', 'custom-menu-item-types' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-rcmit-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="highlight_box">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php _e( 'Highlight Box', 'custom-menu-item-types' ); ?>">
					</li>
					<li>
						<label class="menu-item-title">
							<input type="radio" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php _e( 'Shortcode Box', 'custom-menu-item-types' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-rcmit-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="shortcode_box">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php _e( 'Shortcode Box', 'custom-menu-item-types' ); ?>">
					</li>
				</ul>
			</div>
			<input type="hidden" value="custom" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" />

			<p class="button-controls wp-clearfix">
				<span class="add-to-menu">
					<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'custom-menu-item-types' ); ?>" name="add-custom-menu-item" id="submit-custom-item-types" />
					<span class="spinner"></span>
				</span>
			</p>

		</div><!-- /.custom-item-types -->
		<?php
	}

	public function wp_update_nav_menu_item( $menu_id = 0, $menu_item_db_id = 0, $args ) {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		// Add new menu item via ajax.
		if ( isset( $_REQUEST['menu-settings-column-nonce'] ) && wp_verify_nonce( $_REQUEST['menu-settings-column-nonce'], 'add-menu_item' ) ) {
			if ( ! empty( $_POST['menu-item']['-1']['menu-item-url'] ) && in_array( $_POST['menu-item']['-1']['menu-item-url'], array( 'shortcode_box', 'highlight_box' ) ) ) {
				update_post_meta(
					$menu_item_db_id,
					'_menu_item_rcmit_type',
					sanitize_text_field( $_POST['menu-item']['-1']['menu-item-url'] )
				);
				update_post_meta(
					$menu_item_db_id,
					'_menu_item_url',
					''
				);
			}
		}

		// Update settings for existing menu items.
		if ( isset( $_REQUEST['update-nav-menu-nonce'] ) && wp_verify_nonce( $_REQUEST['update-nav-menu-nonce'], 'update-nav_menu' ) ) {
			if ( ! empty( $_POST['menu-item-button-text'][ $menu_item_db_id ] ) ) {
				update_post_meta(
					$menu_item_db_id,
					'_menu_item_rcmit_button_text',
					sanitize_text_field( $_POST['menu-item-button-text'][ $menu_item_db_id ] )
				);
			}

			if ( ! empty( $_POST['menu-item-shortcode'][ $menu_item_db_id ] ) ) {
				update_post_meta(
					$menu_item_db_id,
					'_menu_item_rcmit_shortcode',
					sanitize_text_field( $_POST['menu-item-shortcode'][ $menu_item_db_id ] )
				);
			}

			if ( ! empty( $_POST['menu-item-column'][ $menu_item_db_id ] ) ) {
				update_post_meta(
					$menu_item_db_id,
					'_menu_item_rcmit_column',
					sanitize_text_field( $_POST['menu-item-column'][ $menu_item_db_id ] )
				);
			}

			if ( ! empty( $_POST['menu-item-heading-level'][ $menu_item_db_id ] ) ) {
				update_post_meta(
					$menu_item_db_id,
					'_menu_item_rcmit_heading_level',
					sanitize_text_field( $_POST['menu-item-heading-level'][ $menu_item_db_id ] )
				);
			}
		}

	}

}
