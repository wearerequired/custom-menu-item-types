<?php

namespace required\Custom_Menu_Item_Types;

function bootstrap() {
	$rcmit_plugin = new Plugin();
	$rcmit_plugin->run();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\bootstrap' );
