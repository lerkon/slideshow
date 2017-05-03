<?php
/*
  Plugin name: Slideshow
  Version: 123
*/

add_action('admin_enqueue_scripts','loadScripts');
// add_action('wp_enqueue_script','loadScripts');
add_action('init', 'initSlideshowMenu');
add_action('add_meta_boxes', 'imageLoaderFrame');
add_action('save_post', 'save_all_data');
// add_action('wp_loaded', 'mozeZaladowane');
add_shortcode('start_slideshow', 'zacznij' );
add_action( 'zacznijTeraz', 'zacznij');

function zacznij()
{
  wp_deregister_script('jquery');
  loadScripts();
  global $wpdb;
  $post = $wpdb->get_results($wpdb->prepare(
    "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='slideshow'",
    sanitize_text_field("slideshow1")
  ));
  $list = get_post_meta($post[0]->ID, 'slideshowPost', true);
  ?>
      <h2 class="widget-title"><?php echo $instance['title']; ?></h2>
        <div id="slides" style="width: 300px;">
          <?php
            foreach ($list as $key) {
              $src = wp_get_attachment_url($key['pic']);
              echo "<img id='image-preview' src='{$src}'>";
            }
          ?>
        </div>
    <script type="text/javascript">
      window.onload = function() {
        jQuery(function() {
          jQuery("#slides").slidesjs({
            width: 300,
            height: 250,
            play: {
              active: true,
              auto: true,
              interval: 3000,
              swap: true
            }
          });
        });
      };
    </script>
  <?php
}

function initSlideshowMenu() {
	register_post_type('slideshow',
		array(
			'labels' => array(
				'name' => __( 'Slideshow' ),
				'singular_name' => __( 'Slideshow' )
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'slideshow'), // 'supports' => array('title', 'editor', 'author', 'thumbnail'),
		)
	);
}

function imageLoaderFrame()
{
  add_meta_box(
    'slideshow',
    'Gallery',
    'contentImageLoader',
    'slideshow' // name of register post type
  );
}

function loadScripts() {
  wp_register_script( 'jquery.slides', plugins_url( '/jquery.slides.js', __FILE__ ), array( 'jquery' ) );
  wp_enqueue_script( 'jquery.slides' );
  wp_register_style( 'slideshowFrame', plugins_url( '/playing/main.css', __FILE__ ) );
  wp_enqueue_style('slideshowFrame');
}

function save_all_data($post_id)
{ // if (isset($_POST['author']) && !empty($_POST['author'])) {
  $list = get_option("tempPic_{$post_id}'");
  if ($list !== false) {
    update_post_meta(
      $post_id,
      'slideshowPost',
      get_option("tempPic_{$post_id}'")
    );
  }
}

function contentImageLoader($data)
{
  $aaa =  get_post_meta($data->ID);
  $start = $aaa['moja_time_start'][0];
  $end = $aaa['moja_time_end'][0]; // $time = date("Y-m-d H:i:s);
  ?>
    <div>
      <div class='form-control'>
        <label>
          Width<br>
          <input type='number' name='width'/>
        </label><br>
        <label>Height<br>
          <input type='number' name='height'/>
        </label><br>
        <label>Text<br>
          <input type='text' name='text'/>
        </label>
        <form method='post'>
      		<div class='image-preview-wrapper'>
      			<img id='image-preview' height='100'>
      		</div>
      		<input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
          <input type='hidden' name='image_attachment_id' id='image_attachment_id' value='<?php echo get_option( 'media_selector_attachment_id' ); ?>'>
      		<input type='hidden' name='post_id' id='post_id' value='<?php echo $data->ID; ?>'>
      		<input type="submit" name="submit_image_selector" value="Save" class="button-primary">
      	</form>
      </div>
      <hr>
      <div>
        <?php
          $list = get_option("tempPic_{$data->ID}'");
          foreach ($list as $key) {
            ?>
              <div class='image-preview-wrapper'  style="display: inline;">
                <img id='image-preview' height='100' src='<?php echo wp_get_attachment_url($key['pic']); ?>'>
              </div>
            <?php
          }
        ?>
      </div>
    </div>
  <?php
  $my_saved_attachment_post_id = get_option( 'media_selector_attachment_id', 0 );

  ?><script type='text/javascript'>
    window.onload = function() {
      jQuery(function() {
        jQuery("#slides").slidesjs({
          width: 940,
          height: 528,
          play: {
            active: true,
            auto: true,
            interval: 3000,
            swap: true
          }
        });
      });
    };

    jQuery( document ).ready( function( $ ) {
      var file_frame;
      var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
      var set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>; // Set this
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
  </script><?php
  }
?>

<?php // Save attachment ID, innymi słowy swtórz tymczasowy objekt w bazie danych
  if ( isset( $_POST['submit_image_selector'] ) && isset( $_POST['image_attachment_id'] ) )
  {
    $tempPic = "tempPic_{$_POST['post_id']}'";
    $list = get_option($tempPic);
    if ($list === false) { $list = []; }
    $aaa = [];
    $aaa['pic'] = absint($_POST['image_attachment_id']);
    $aaa['width'] = $_POST['width'];
    $aaa['height'] = $_POST['height'];
    $aaa['text'] = $_POST['text'];
    array_push($list, $aaa);
    update_option($tempPic, $list); // update_option( 'media_selector_attachment_id', absint( $_POST['image_attachment_id'] ) );
  }
?>
