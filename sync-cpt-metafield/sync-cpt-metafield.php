<?php
/**
 * Plugin Name:       Sync CPT meta fields
 * Plugin URI:        https://perelom.com
 * Description:       Syncronize CPT meta fields (Organization and Services)
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Buria
  * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       sync-cpt-metafield
 */


/**
 * Copy linked IDs of organisation CPT to org_id meta field of all services CPT
 * @return void
 */
function get_org_by_service(): void {
	global $wpdb;
    $service_ids = get_posts( [
            'post_type' => 'services',
            'fields' => 'ids'
    ] );
    foreach( $service_ids as $service_id) {
        $sql = $wpdb->prepare("SELECT wp_postmeta.meta_value FROM wp_postmeta
                                    INNER JOIN wp_jet_rel_default ON wp_postmeta.post_id = wp_jet_rel_default.parent_object_id
                                    INNER JOIN wp_posts ON wp_jet_rel_default.child_object_id = wp_posts.ID
                                    WHERE wp_posts.ID = %s AND wp_postmeta.meta_key = 'id' ", $service_id );
        $org_ids = $wpdb->get_col( $sql );
        update_post_meta( $service_id, 'org_id', implode(',', $org_ids ) );
    }
}

/**
 * Copy linked IDs of organisation CPT to org_id meta field of services CPT when service CPT is saved via hook
 *
 * @param integer $post_id
 * @param object $post
 *
 * @return void
 */
function update_org_by_service( $post_id, $post ) {
	// bail out if this is an autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if( 'services' != $post->post_type ) {
		return;
	}

	global $wpdb;
	$sql = $wpdb->prepare("SELECT wp_postmeta.meta_value FROM wp_postmeta
                                INNER JOIN wp_jet_rel_default ON wp_postmeta.post_id = wp_jet_rel_default.parent_object_id
                                INNER JOIN wp_posts ON wp_jet_rel_default.child_object_id = wp_posts.ID
                                WHERE wp_posts.ID = %s AND wp_postmeta.meta_key = 'id' ", $post_id );
	$org_ids = $wpdb->get_col( $sql );
	update_post_meta( $post_id, 'org_id', implode(',', $org_ids ) );
}

/**
 * Shortcode to copy org IDs for all services CPT
 */
add_shortcode('sqltest', 'get_org_by_service');

add_action( 'save_post', 'update_org_by_service', 20, 2);
