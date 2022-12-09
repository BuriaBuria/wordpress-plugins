<?php
/**
 * Plugin Name:       Post in modal window with REST (Bootstrap 5)
 * Plugin URI:        https://perelom.com
 * Description:       Shows post in a modal window with Bootstrap 5.0 and REST (without jquery)
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            buria@perelom.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

function add_bootstrap_modal() {
	?>

    <div class="modal fade" id="postModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalTitle">Loading...</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <p>Loading...</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

	<?php
}

function show_post_in_modal( $atts ) {
	$attributes = shortcode_atts( [
		'id' => 0,
		'text' => 'Click to show post',
		'class' => 'btn btn-primary',
		'style' => ''
	], $atts );

	wp_enqueue_style( 'bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css' );
	wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js', [], '', true );
	wp_enqueue_script( 'postmodal-js', plugin_dir_url(__FILE__).'assets/js/main.js', [], '', true );
	wp_add_inline_script( 'postmodal-js', 'const wpApiSettings = ' . json_encode( [
            'url' => rest_url('/postmodal/v1/post/') . $attributes['id'] . '/',
            'nonce' => wp_create_nonce( 'wp_rest' ),
		] ), 'before' );

	add_action( 'wp_footer', 'add_bootstrap_modal');

	ob_start();

	?>
    <button type="button" class="<?php echo $attributes['class']; ?>" style="<?php echo $attributes['style']; ?>" data-bs-toggle="modal" data-bs-target="#postModal">
		<?php echo $attributes['text']; ?>
    </button>

	<?php

    $output = ob_get_clean();

	return $output;
}
add_shortcode( 'modal_post', 'show_post_in_modal' );


add_action( 'rest_api_init', function(){
	$namespace = 'postmodal/v1';
	$rout = '/post/(?P<id>\d+)';
	$rout_params = [
		'methods'  => 'GET',
		'callback' => 'get_post_modal',
		'args'     => [
			'id' => [
				'type'    => 'integer',
				'default' => 0,
			],
		],
		'permission_callback' => function( $request ){
			return is_user_logged_in();
		},
	];

	register_rest_route( $namespace, $rout, $rout_params );

} );

function get_post_modal( WP_REST_Request $request ) {
    $post_modal = get_post( $request['id'] );
	if( ! $post_modal ||  'post' !== $post_modal->post_type ) {
		$title = 'Error! Post not found.';
        $content = 'Please check post ID.';
	} else {
		$title = $post_modal->post_title;
		$content = preg_replace('/\n/', '', apply_filters( 'the_content', $post_modal->post_content ) );
	}

	return ( ['title' => $title, 'content' => $content ] );
}