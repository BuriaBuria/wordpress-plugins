<?php
/**
 * Plugin Name:       Copy meta field
 * Plugin URI:        https://perelom.com
 * Description:       Copy meta field from Organization CPT to Services CPT
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            buria@perelom.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

class CopyMetaField {

	public function __construct(){
		add_action( 'admin_menu', array( $this, 'setup_admin_copy_meta_menu' ) );
		add_action( 'rest_api_init', array( $this, 'register_route') );
		add_action( 'save_post', array( $this, 'update_org_by_service' ), 20, 2);
	}

	/**
	 * Copy meta field from parent Organisation CPT to child Services CPT for the post of Services CPT
	 *
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function copy_meta_field( $post_id ) {
		global $wpdb;
		$sql = $wpdb->prepare("SELECT {$wpdb->prefix}postmeta.meta_value FROM {$wpdb->prefix}postmeta
                                    INNER JOIN {$wpdb->prefix}jet_rel_default ON {$wpdb->prefix}postmeta.post_id = {$wpdb->prefix}jet_rel_default.parent_object_id
                                    INNER JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}jet_rel_default.child_object_id = {$wpdb->prefix}posts.ID
                                    WHERE {$wpdb->prefix}posts.ID = %s AND {$wpdb->prefix}postmeta.meta_key = 'id' ", $post_id );
		$org_ids = $wpdb->get_col( $sql );
		$result = update_post_meta( $post_id, 'org_id', implode(',', $org_ids ) );
		return [ $result, $org_ids ];
	}

	/**
	 * Copy meta for all posts of Services CPT
	 * @return void
	 */
	public function get_org_by_service(): void {

		$service_ids = get_posts( [
			'post_type' => 'services',
			'fields' => 'ids'
		] );

		foreach( $service_ids as $service_id) {
			$result = $this->copy_meta_field( $service_id ) ;
			$result[0] = $result[0] ? ' was copied: ' : ' no change: ';
			echo '<div>Service ' . $service_id . $result[0] . implode( ',', $result[1] ) . '</div>';
		}
		echo '<div><strong>Process completed.</strong></div>';
	}

	/**
	 * Copy meta field from parent Organisation CPT to child Services CPT for the new or updated post of Services CPT
	 *
	 * @param integer $post_id
	 * @param object $post
	 *
	 * @return void
	 */
	public function update_org_by_service( $post_id, $post ): void {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if( 'services' != $post->post_type ) {
			return;
		}

		$this->copy_meta_field( $post_id );
	}

	public function setup_admin_copy_meta_menu(): void {
		add_submenu_page( 'edit.php?post_type=services','Copy meta filed', 'Copy meta', 'activate_plugins', 'copy-meta', array( $this, 'admin_copy_meta_html') );
	}

	public function admin_copy_meta_html():void {
		$html = <<< HEREDOC
		<h2 class="copymeta-header">Copy meta field from parent Organisation CPT to child Services CPT</h2>
		<p class="copymeta-description">Click below to copy meta field from parent Organisation CPT to child Services CPT for all posts of Services CPT</p>
		<button class="copymeta-init">Copy meta fields</button>
		HEREDOC;
		echo $html;
		wp_enqueue_script( 'copy-metafield', plugin_dir_url( __FILE__ ) . '/assets/js/copy-metafield-admin.js', '', '', true );
		$rest_data = [
			'endpoint' => site_url( '/wp-json/copy/meta/'),
			'nonce' => wp_create_nonce( 'wp_rest' )
		];
		wp_add_inline_script( 'copy-metafield', 'const RESTDATA = ' . json_encode( $rest_data  ), 'before' );
	}

	public function register_route():void {
		register_rest_route( 'copy/', 'meta/', array(
			'methods' => 'GET',
			'callback' => array ($this, 'get_org_by_service'),
			'permission_callback' => function ( ) {
				return current_user_can( 'manage_options' );
			},
		) );
	}
}

$copymetafield = new CopyMetaField();

