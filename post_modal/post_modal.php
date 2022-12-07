<?php
/**
 * Plugin Name:       Post in modal window (Bootstrap 5)
 * Plugin URI:        https://perelom.com
 * Description:       Shows post in a modal window with Bootstrap 5.0 (without jquery)
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            buria@perelom.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

function modal_enqueue_bootstrap() {
	wp_enqueue_style( 'bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css' );
	wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js', [], '', true );
}
add_action( 'wp_enqueue_scripts', 'modal_enqueue_bootstrap');

function add_bootstrap_modal() {
	?>

	<div class="modal fade" id="postModal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
                    <h4 class="modal-title" id="modalTitle">Post title</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body" id="modalBody">
					<p>Post text</p>
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

	add_action( 'wp_footer', 'add_bootstrap_modal');

    $post_modal = get_post( $attributes['id'] );
    if( ! $post_modal ||  'post' !== $post_modal->post_type ) {
	    $post_title = 'Error';
	    $post_content = 'No post';
    } else {
	    $post_title = $post_modal->post_title;
	    $post_content = preg_replace('/\n/', '', $post_modal->post_content);
    }
	?>

    <script>
       document.addEventListener("DOMContentLoaded", () => {
            document.getElementById('postModal').addEventListener('show.bs.modal', modalShowPost)
       } )

       function modalShowPost() {
            document.getElementById('modalTitle').textContent = '<?php echo $post_title; ?>'
            document.getElementById('modalBody').innerHTML = '<?php echo $post_content; ?>'
       }

    </script>
    <button type="button" class="<?php echo $attributes['class']; ?>" style="<?php echo $attributes['style']; ?>" data-bs-toggle="modal" data-bs-target="#postModal">
	    <?php echo $attributes['text']; ?>
    </button>

	<?php
}
add_shortcode( 'modal_post', 'show_post_in_modal' );
