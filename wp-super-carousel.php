<?php

/**
 * Plugin Name: Carousels
 * Version:     0.1
 **/

function arti_carousel_item( $item_title = '', $item_image = 0, $item_content = '', $item_link_url = '', $item_link_text = '', $item_link_target = '_parent' ) {
	$img_src = $item_image > 0 ? wp_get_attachment_image( $item_image, 'medium' ) : '';
	if ( $item_link_text == '' ) {
		$item_link_text = __( 'Read more', 'arti-carousel' );
	}
	if ( $check_button_video == '' ) {
		$check_button_video = 'image';
	}
	$item_new = '
				<li class="carousel-item clearfix open">
				<div class="carousel-header">
				<input name="carousel_title[]" class="carousel-title large-text" type="text" value="' . $item_title . '" placeholder="' . __( 'Headline', 'arti-carousel' ) . '" />
				<div class="carousel-menu">
				<span class="action remove dashicons dashicons-trash"></span>
				<span class="action move handle dashicons dashicons-editor-ol"></span>
				</div>
				</div>
				<div class="carousel-body">
				<div class="carousel-image photo-item clearfix">
				<div class="video">
				<div class="choose-video">
				<label for="carousel_video[]">' . __( 'Choose Content-Mode?', 'arti-carousel' ) . '</label>
				<input type="hidden" name="carousel_video[]" value="' . $check_button_video . '" class="type"/>
				<select class="selectmenuvideo"><option value="image">' . __( 'Image Mode', 'arti-carousel' ) . '</option><option value="video">' . __( 'Video Mode', 'arti-carousel' ) . '</option></select>
				</div>
				<div class="video-content">
				<div class="video-preview "></div>
				<button type="button" class="video-change button button-secondary">' . __( 'Select Video', 'arti-carousel' ) . '</button>
				<label for="carousel_video_youtube[]" class="label-block">' . __( 'Youtube Link', 'arti-carousel' ) . '</label>
				<input name="carousel_video_youtube[]" class="carousel-video_youtube carousel_link" type="text" value="' . $item_video_youtube . '" placeholder="' . __( 'Youtube-Link', 'arti-carousel' ) . '" />
				<label for="carousel_video_vimeo[]" class="label-block">' . __( 'Vimeo Link', 'arti-carousel' ) . '</label>
				<input name="carousel_video_vimeo[]" class="carousel-video_vimeo carousel_link" type="text" value="' . $item_video_vimeo . '" placeholder="' . __( 'Vimeo-Link', 'arti-carousel' ) . '" />
				</div>
				</div>
				<div class="photo-preview ">' . $img_src . '</div>
				<button type="button" class="photo-change button button-secondary">' . __( 'Select Image', 'arti-carousel' ) . '</button>
				<input name="carousel_image[]" class="photo-id" type="hidden" value="' . $item_image . '" />
				</div>
				<textarea name="carousel_content[]" class="carousel-content large-text" rows="8" placeholder="' . __( 'Write Your Content', 'arti-carousel' ) . '">' . $item_content . '</textarea>
				</div>
				<div class="carousel-footer">
				<div class="input-container">
				<label for="carousel_link_text[]" class="label-block">' . __( 'Link Text', 'arti-carousel' ) . '</label>
				<input name="carousel_link_text[]" class="carousel-link_text carousel_link" type="text" value="' . $item_link_text . '" placeholder="' . __( 'Link-Text', 'arti-carousel' ) . '" />
				</div>
				<div class="input-container">
				<label for="carousel_link_url[]" class="label-block">' . __( 'Link Url', 'arti-carousel' ) . '</label>
				<input name="carousel_link_url[]" class="carousel-link_url carousel_link" type="text" value="' . $item_link_url . '" placeholder="' . __( 'Link-Url', 'arti-carousel' ) . '" />
				</div>
				<div class="selectmenu-container">
				<label for="arti_carousel_content_link_target" class="label-block">' . __( 'Link Target', 'arti-carousel' ) . '</label>
				<input type="hidden" name="carousel_link_target[]" value="' . $item_link_target . '" />
				<select class="selectmenu"><option value="_parent">' . __( 'In the same window', 'arti-carousel' ) . '</option><option value="_blank">' . __( 'In a new window', 'arti-carousel' ) . '</option></select>
				</div>
				</div>
			</li>';
	$item_new = trim( preg_replace( '/\s\s+/', ' ', $item_new ) );

	return $item_new;
}


function arti_carousel_form_script() {
	$item_new = arti_carousel_item();
	?>
	<script type="text/javascript">
      jQuery(document).ready(function($) {
//add-item
        $('#carousel-addnew').click(function() {
          $('#carousel-loop').append('<?php echo $item_new;?>');
          $('.selectmenu').each(function(event, ui) {
            $selectValue = $(this).parent().find('input').val();
            $(this).
                parent().
                find('select').
                find('option[value="' + $selectValue + '"]').
                attr('selected', true);
            $(this).chosen().on('change', function() {
              $(this).parent().find('input').val(ui.value);
            });
          });
        });
//remove-item
        $('.carousel-header .remove').live('click', function() {
          if (confirm('Are you sure?')) {
            $(this).
                closest('.carousel-item').
                fadeOut(500, function() {$(this).remove();});
          }
        });
//sortable
        $('#carousel-loop').sortable({handle: '.handle'});

//media upload
        $(document).on('click', '.photo-change', function() {
          var parent = $(this).closest('.photo-item');
          file_frame = wp.media.frames.file_frame = wp.media({
            frame: 'select',
            multiple: false,
            library: {type: 'image'},
          });

          file_frame.on('select', function() {
            var json = file_frame.state().get('selection').first().toJSON();
            console.log(json);
            var att_id = json.id;

            if (json.sizes.hasOwnProperty('medium')) {
              var att_url = json.sizes.medium.url;
            }
            else {
              var att_url = json.sizes.full.url;
            }

//sent-to-form
            parent.children('.photo-id').val(att_id);
            parent.children('.photo-preview').
                html('<img src="' + att_url + '" />');
          });

          file_frame.open();
        });
      });//jQuery
	</script>
	<?php
}

add_action( 'save_post', 'arti_carousel_save' ); //Save Code
function arti_carousel_save( $post_id ) {
	//Security
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	};
	//Nonce-Field Check
	if ( ! isset( $_POST['arti_carousel_meta_box_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['arti_carousel_meta_box_nonce'], 'arti_carousel_save_meta_box_data' ) ) {
		return;
	}
	//Nonce-Field Check END
	if ( 'arti_carousel' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		};
	};
	//Security END

	//Get Editor Variables
	$titles          = (array) $_POST['carousel_title'];
	$images          = (array) $_POST['carousel_image'];
	$contents        = (array) $_POST['carousel_content'];
	$link_url        = (array) $_POST['carousel_link_url'];
	$link_text       = (array) $_POST['carousel_link_text'];
	$link_target     = (array) $_POST['carousel_link_target'];
	$_arti_carousels = [];
	foreach ( $titles as $key => $val ) {
		$_arti_carousels[] = [
			'title'       => sanitize_text_field( $titles[ $key ] ),
			'image'       => intval( $images[ $key ] ),
			'content'     => sanitize_textarea_field( $contents[ $key ] ),
			'link_url'    => sanitize_text_field( $link_url[ $key ] ),
			'link_text'   => sanitize_text_field( $link_text[ $key ] ),
			'link_target' => sanitize_text_field( $link_target[ $key ] ),
		];
	};

	//Fields - Basic Settings
	include( 'metabox-save/fields-basic-setings.php' );
	//Fields - Formattings
	include( 'metabox-save/fields-formattings.php' );
	//Fields - Expert Mode
	include( 'metabox-save/fields-expert.php' );
	//Load CSS Code for File
	include( 'metabox-save/getCSS.php' );
	//Load JavaScript Code for File
	include( 'metabox-save/getJavaScript.php' );

	cssfile( $post_id, $cssFile );
	jsfile( $post_id, $jsFile );

	update_post_meta( $post_id, '_arti_carousels_editor', ( $_arti_carousels ) );
}

// Output Metabox

