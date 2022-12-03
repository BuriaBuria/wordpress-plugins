<?php
/**
 * Plugin Name:       File download
 * Plugin URI:        https://perelom.com
 * Description:       File download
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            buria@perelom.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

add_shortcode( 'dlink', 'fd_get_file_link' );

function fd_get_file_link( $attachment_id ) {
	ob_start();
	$attachment_id = 31;
	echo '<div><a href="' . get_permalink( $attachment_id ) . '?attachment_id=' . $attachment_id . '&download_file=1">' . get_the_title( $attachment_id) . '</a></div>'; // todo get_the_ID()
	return ob_get_clean();
}

function fd_download_file() {
	if( isset( $_GET['attachment_id'] ) && isset( $_GET['download_file'] ) ) {
		fd_send_file();
	}
}
add_action( 'init', 'fd_download_file' );

function fd_send_file() {
	$att_id = $_GET['attachment_id'];
	$the_file = wp_get_attachment_url( $att_id );
	if ( ! $the_file ) {
		return;
	}
	$file_info = pathinfo( $the_file );
	$whitelist = apply_filters( 'fd_allowed_file_types', array( 'jpg', 'gif', 'zip') );
	if( ! in_array($file_info['extension'], $whitelist ) ) {
		wp_die ('Invalid file!');
	}
	//todo
}
