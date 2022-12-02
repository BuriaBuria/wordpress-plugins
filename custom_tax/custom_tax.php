<?php
/**
 * Plugin Name:       Custom taxonomies
 * Plugin URI:        https://perelom.com
 * Description:       Custom taxonomies and SQL queries
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            buria@perelom.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */


// Register Custom Taxonomy
function register_food_type() {

	$labels = array(
		'name'                       => _x( 'Food types', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Food type', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Food type', 'text_domain' ),
		'all_items'                  => __( 'All Items', 'text_domain' ),
		'parent_item'                => __( 'Parent Item', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New food type', 'text_domain' ),
		'add_new_item'               => __( 'Add New Item', 'text_domain' ),
		'edit_item'                  => __( 'Edit Item', 'text_domain' ),
		'update_item'                => __( 'Update Item', 'text_domain' ),
		'view_item'                  => __( 'View Item', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular Items', 'text_domain' ),
		'search_items'               => __( 'Search Items', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
		'no_terms'                   => __( 'No items', 'text_domain' ),
		'items_list'                 => __( 'Items list', 'text_domain' ),
		'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'show_in_rest'               => true,
	);
	register_taxonomy( 'food_type', array( 'post' ), $args );

}
add_action( 'init', 'register_food_type', 0 );

// Add Shortcode
add_shortcode( 'demo', 'demo' );
function demo() {
	ob_start();
	$query_args = array(
		'post_type' => 'post'
	);
	$term_args = array(
		'fields'=> 'ids'
	);
	$query = new WP_Query( $query_args );
	if( $query->have_posts() ) {
		while( $query->have_posts() ) {
			$query->the_post();
			the_title( '<h3>', '</h3>');
            $related_posts = get_related_posts( get_the_ID(), 2 );
            if( $related_posts ) {
                echo implode( ' ', $related_posts );
            }
		}
	}
	return ob_get_clean();
}

function get_related_posts( $post_id, $matches_required ) {
	if( ! is_numeric( $post_id ) || ! is_numeric( $matches_required ) ) {
        return false;
    }
    $post_id = intval( $post_id );
    if( 0 === $post_id ) {
        return false;
    }
	global $wpdb;
    $sql = $wpdb->prepare("SELECT post_title FROM {$wpdb->posts} WHERE ID IN (
                                    SELECT post_id from (
                                        SELECT t1.object_id as post_id, COUNT(t1.term_taxonomy_id) as term_match
                                        FROM {$wpdb->term_relationships} as t1
                                        INNER JOIN {$wpdb->term_relationships} as t2 ON t1.term_taxonomy_id = t2.term_taxonomy_id
                                        WHERE t2.object_id = %s
                                        GROUP BY t1.object_id
                                        HAVING term_match > %s AND post_id != %s 
                                        ) as related_content
                                    )", $post_id, $matches_required, $post_id );
	$related_posts = $wpdb->get_col( $sql );
	if( 0 === count( $related_posts ) ) {
        return false;
	}
    return $related_posts;
}

function show_related_posts( $content ) {
    if( is_singular( 'post' ) ) {
        global $post;
        $related_posts = get_related_posts( get_the_ID(), 2 );
        if( $related_posts ) {
	        $related_content = '<div><strong>' . __( 'Related posts' ) . ':</strong> ' . implode( ', ', $related_posts ) . '</div>';
	        $content .= $related_content;
        }
    }
    return $content;
}

add_filter( 'the_content', 'show_related_posts', 10, 1);



/*
 * Add/Edit/Save Taxonomy custom fields
 */

add_action( 'food_type_add_form_fields', 'add_food_taxonomy_fields' );

function add_food_taxonomy_fields() {
	?>
	<div class="form-field">
		<label for="location">Location field</label>
		<input type="text" name="location" id="location">
		<p>Please input location</p>
	</div>
	<?php
}

add_action( 'food_type_edit_form_fields', 'edit_food_taxonomy_fields', 10, 1);

function edit_food_taxonomy_fields( $term ) {
    $location = get_term_meta( $term->term_id, 'location', true);
    ?>
    <tr class="form-field">
        <th><label for="location">Location field</label></th>
        <td>
            <input type="text" name="location" id="location" value="<?php echo esc_attr( $location ); ?>">
            <p>Please input location</p>
        </td>
    </tr>

    <?php
}

add_action( 'created_food_type', 'save_food_taxonomy_fields');
add_action( 'edited_food_type', 'save_food_taxonomy_fields');
function save_food_taxonomy_fields( $term_id ) {
    update_term_meta( $term_id, 'location', sanitize_text_field( $_POST['location'] ) );
}

