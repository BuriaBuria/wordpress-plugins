<?php
/**
 * Plugin Name:       Store Hours admin page patch
 * Plugin URI:        https://perelom.com
 * Description:       Add custom classes to DOM element on Store Hours admin page
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            buria@perelom.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

class ShBackendPatch {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'sh_load_backend_js' ) );
	}

	public function sh_load_backend_js( $hook ):void {
//		if( 'admin.php' != $hook  ) {
//			return;
//		}
		wp_enqueue_script( 'sh_backend_patch', plugin_dir_url( __FILE__ ) . '/admin/js/backend_patch.js', '', '', true );
	}
}

$sh_backend_patch = new ShBackendPatch();