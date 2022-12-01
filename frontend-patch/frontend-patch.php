<?php
/**
 * Plugin Name:       Frontend patch
 * Plugin URI:        https://perelom.com
 * Description:       Change specific DOM element
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            buria@perelom.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

class FrontendPatch {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_js' ) );
	}

	public function load_frontend_js():void {
		if( is_page( array( 'calendar' ) ) ) {
			wp_enqueue_script( 'frontend_patch', plugin_dir_url( __FILE__ ) . '/assets/js/frontend-patch.js', '', '', true );
			$discount = '0.7';
			wp_add_inline_script( 'frontend_patch', 'const DISCOUNT = ' . $discount, 'before' );
		}
	}
}

$frontend_patch = new FrontendPatch();