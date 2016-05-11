<?php
/**
 * Plugin Name: Custom Menu Item Types
 * Plugin URI:  https://github.com/wearerequired/custom-menu-item-types/
 * Description: Additional menu item types that can be easily added to the menu like line breaks and titles.
 * Version:     0.1.0
 * Author:      required+
 * Author URI:  http://required.ch
 * License:     GPLv2+
 * Text Domain: menu-item-types
 * Domain Path: /languages
 *
 * @package Menu_Item_Types
 */

/**
 * Copyright (c) 2016 required+ (email : support@required.ch)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace required\Custom_Menu_Item_Types;

defined( 'WPINC' ) or die;

if ( ! defined( 'RCMIT_FILE' ) ) {
	define( 'RCMIT_FILE', __FILE__ );
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

$menu_item_types_requirements_check = new \WP_Requirements_Check( array(
	'title' => 'Custom Menu Item Types',
	'php'   => '5.3',
	'wp'    => '4.4',
	'file'  => __FILE__,
) );

if ( $menu_item_types_requirements_check->passes() ) {
	// Pull in the plugin classes and initialize.
	$rcmit_plugin = new Plugin();
	$rcmit_plugin->run();
}

// Unset, since it's loaded in global scope
unset( $menu_item_types_requirements_check );
