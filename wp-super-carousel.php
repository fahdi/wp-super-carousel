<?php

/**
 * Plugin Name: Carousels
 * Version:     0.1
 **/

function arti_carousel_item( $item_title = '', $item_image = 0, $item_content = '', $item_link_url = '', $item_link_text = '', $item_link_target = '_parent', $check_button_video = 'image', $item_video_youtube = '', $item_video_vimeo = '' ) {
	$item_image = (int) $item_image;
	$img_src    = $item_image > 0 ? wp_get_attachment_image( $item_image, 'medium' ) : '';
	if ( $item_link_text == '' ) {
		$item_link_text = __( 'Read more', 'arti-carousel' );
	}
	if ( $check_button_video == '' ) {
		$check_button_video = 'image';
	}
	$item_title         = esc_attr( $item_title );
	$item_content       = esc_textarea( $item_content );
	$item_link_url      = esc_attr( $item_link_url );
	$item_link_text     = esc_attr( $item_link_text );
	$item_link_target   = esc_attr( $item_link_target );
	$check_button_video = esc_attr( $check_button_video );
	$item_video_youtube = esc_attr( $item_video_youtube );
	$item_video_vimeo   = esc_attr( $item_video_vimeo );
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
          $('#carousel-loop').append(<?php echo wp_json_encode( $item_new ); ?>);
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
        $(document).on('click', '.carousel-header .remove', function() {
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
	if ( 'arti_carousel' !== get_post_type( $post_id ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	//Security END

	//Get Editor Variables
	$post_array = wp_unslash( $_POST );
	$titles          = isset( $post_array['carousel_title'] ) ? (array) $post_array['carousel_title'] : [];
	$images          = isset( $post_array['carousel_image'] ) ? (array) $post_array['carousel_image'] : [];
	$contents        = isset( $post_array['carousel_content'] ) ? (array) $post_array['carousel_content'] : [];
	$link_url        = isset( $post_array['carousel_link_url'] ) ? (array) $post_array['carousel_link_url'] : [];
	$link_text       = isset( $post_array['carousel_link_text'] ) ? (array) $post_array['carousel_link_text'] : [];
	$link_target     = isset( $post_array['carousel_link_target'] ) ? (array) $post_array['carousel_link_target'] : [];
	$_arti_carousels = [];
	foreach ( $titles as $key => $val ) {
		$target            = isset( $link_target[ $key ] ) ? sanitize_text_field( $link_target[ $key ] ) : '_parent';
		$_arti_carousels[] = [
			'title'       => sanitize_text_field( $val ),
			'image'       => isset( $images[ $key ] ) ? intval( $images[ $key ] ) : 0,
			'content'     => isset( $contents[ $key ] ) ? sanitize_textarea_field( $contents[ $key ] ) : '',
			'link_url'    => isset( $link_url[ $key ] ) ? esc_url_raw( $link_url[ $key ] ) : '',
			'link_text'   => isset( $link_text[ $key ] ) ? sanitize_text_field( $link_text[ $key ] ) : '',
			'link_target' => in_array( $target, [ '_parent', '_blank' ], true ) ? $target : '_parent',
		];
	};

	// Optional extra field handlers / generated CSS+JS. These files are not
	// part of this repository; include them only when present so saving a
	// post never fatals on a missing include or undefined cssfile()/jsfile().
	$cssFile = '';
	$jsFile  = '';
	foreach ( [ 'fields-basic-setings.php', 'fields-formattings.php', 'fields-expert.php', 'getCSS.php', 'getJavaScript.php' ] as $extra ) {
		$path = __DIR__ . '/metabox-save/' . $extra;
		if ( file_exists( $path ) ) {
			include $path;
		}
	}
	if ( function_exists( 'cssfile' ) ) {
		cssfile( $post_id, $cssFile );
	}
	if ( function_exists( 'jsfile' ) ) {
		jsfile( $post_id, $jsFile );
	}

	update_post_meta( $post_id, '_arti_carousels_editor', $_arti_carousels );
}

// Output Metabox

