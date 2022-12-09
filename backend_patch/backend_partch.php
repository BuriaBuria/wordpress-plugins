<?php
/**
 * Plugin Name:       Store Hours admin page fix
 * Plugin URI:        https://perelom.com
 * Description:       Store Hours admin page fix
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            buria@perelom.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

class BackendPatch {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'load_backend_js' ) );
	}

	public function load_backend_js( $hook ):void {
//		if( 'admin.php' != $hook  ) {
//			return;
//		}
		wp_enqueue_script( 'backend_patch', plugin_dir_url( __FILE__ ) . '/assets/js/backend_patch.js', '', '', true );
	}
}

$backend_patch = new BackendPatch();