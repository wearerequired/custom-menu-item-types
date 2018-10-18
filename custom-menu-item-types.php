<?php
/**
 * Plugin Name: Custom Menu Item Types
 * Plugin URI:  https://github.com/wearerequired/custom-menu-item-types/
 * Description: Additional menu item types that can be easily added to the menu like line breaks and headings.
 * Version:     1.0.0
 * Author:      required
 * Author URI:  https://required.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: custom-menu-item-types
 *
 * @package Menu_Item_Types
 */

/**
 * Copyright (c) 2016-2018 required (email: info@required.com)
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

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require dirname( __FILE__ ) . '/vendor/autoload.php';
}

if ( ! class_exists( 'WP_Requirements_Check' ) ) {
	trigger_error( sprintf( '%s does not exist. Check Composer\'s autoloader.', 'WP_Requirements_Check' ), E_USER_WARNING );
	return;
}

$requirements_check = new WP_Requirements_Check( array(
	'title' => 'Custom Menu Item Types',
	'php'   => '5.3',
	'wp'    => '4.4',
	'file'  => __FILE__,
) );


if ( $requirements_check->passes() ) {
	require_once dirname( __FILE__ ) . '/inc/namespace.php';
}

unset( $requirements_check );
