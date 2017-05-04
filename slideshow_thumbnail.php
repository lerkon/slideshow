<script type='text/javascript'>
  jQuery( document ).ready( function( $ ) {
    var file_frame;
    var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
    var set_to_post_id = <?php echo get_option( 'media_selector_attachment_id', 0 ); ?>; // Set this
    jQuery('#upload_image_button').on('click', function( event ){
      event.preventDefault();
      if ( file_frame ) { // If the media frame already exists, reopen it.
        // Set the post ID to what we want
        file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
        file_frame.open(); // Open frame
        return;
      } else { // Set the wp.media post id so the uploader grabs the ID we want when initialised
        wp.media.model.settings.post.id = set_to_post_id;
      }
      file_frame = wp.media.frames.file_frame = wp.media({ // Create the media frame.
        title: 'Select a image to upload',
        button: {
          text: 'Use this image',
        },
        multiple: false	// Set to true to allow multiple files to be selected
      });
      file_frame.on( 'select', function() { // When an image is selected, run a callback.
        // We set multiple to false so only get one image from the uploader
        attachment = file_frame.state().get('selection').first().toJSON();
        // Do something with attachment.id and/or attachment.url here
        $( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
        $( '#image_attachment_id' ).val( attachment.id );
        wp.media.model.settings.post.id = wp_media_post_id; // Restore the main post ID
      });
        file_frame.open(); // Finally, open the modal
    });
    // Restore the main ID when the add media button is pressed
    jQuery( 'a.add_media' ).on( 'click', function() {
      wp.media.model.settings.post.id = wp_media_post_id;
    });
  });
</script>
