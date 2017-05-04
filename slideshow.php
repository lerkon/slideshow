<?php
/*
  Plugin name: Slideshow
  Version: 123
*/

add_action('admin_enqueue_scripts','loadScripts');
add_action('init', 'initSlideshowMenu');
add_action('add_meta_boxes', 'imageLoaderFrame');
add_action('save_post', 'save_all_data');
add_shortcode('start_slideshow_now', 'place_code_theme' );

function place_code_theme($atts = [], $content = null)
{
  global $wpdb;
  $post = $wpdb->get_results($wpdb->prepare(
    "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='slideshow'",
    sanitize_text_field($atts['post'])
  ));
  $list = get_post_meta($post[0]->ID, 'slideshowPost', true);
  ?>
    <div class="" style="position: relative;">
      aaa
      <div id="slides" style="width: 300px;">
        <?php
          foreach ($list as $key) {
            $src = wp_get_attachment_url($key['pic']);
            echo "<img id='image-preview' src='{$src}'>";
          }
        ?>
      </div>
      bbb
    </div>
  <?php
  apply_slidejs($post[0]->ID);
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
			'rewrite' => array('slug' => 'slideshow'),
		)
	);
}

function imageLoaderFrame()
{
  add_meta_box(
    'slideshow',
    'Gallery',
    'contentImageLoader',
    'slideshow'
  );

}

function loadScripts() {
  wp_register_script( 'jquery.slides', plugins_url( '/jquery.slides.jss', __FILE__ ), array( 'jquery' ) );
  wp_enqueue_script( 'jquery.slides' );
  wp_register_style( 'slideshowFrame', plugins_url( '/playing/main.css', __FILE__ ) );
  wp_enqueue_style('slideshowFrame');
}

function save_all_data($post_id)
{
  $list = get_option("tempPic_{$post_id}'");
  if ($list !== false) {
    update_post_meta(
      $post_id,
      'slideshowPost',
      get_option("tempPic_{$post_id}'")
    );
  }
  if (isset($_POST['width']) && !empty($_POST['width']))
  {
    update_post_meta(
      $post_id,
      'width_slideshow',
      $_POST['width']
    );
  }
  if (isset($_POST['height']) && !empty($_POST['height']))
  {
    update_post_meta(
      $post_id,
      'height_slideshow',
      $_POST['height']
    );
  }
  if (isset($_POST['interval']) && !empty($_POST['interval']))
  {
    update_post_meta(
      $post_id,
      'interval_slideshow',
      $_POST['interval']
    );
  }
}

function apply_slidejs($id)
{
  $width = get_post_meta($id, 'width_slideshow', true);
  $height = get_post_meta($id, 'height_slideshow', true);
  $interval = get_post_meta($id, 'interval_slideshow', true);
  ?><script type='text/javascript'>
    window.onload = function() {
      jQuery(function() {
        jQuery("#slides").slidesjs({
          width: <?php echo $width; ?>,
          height: <?php echo $height; ?>,
          play: {
            active: true,
            auto: true,
            interval: <?php echo $interval * 1000; ?>,
            swap: true
          }
        });
      });
    };
  </script><?php
}

function contentImageLoader($data)
{
  $width = get_post_meta($data->ID, 'width_slideshow', true);
  $height = get_post_meta($data->ID, 'height_slideshow', true);
  $interval = get_post_meta($data->ID, 'interval_slideshow', true);
  require_once("slideshow_post.php");
  require_once("slideshow_thumbnail.php");
}

if (isset( $_POST['submit_image_selector'] ) && isset( $_POST['image_attachment_id'])) {
  $tempPic = "tempPic_{$_POST['post_id']}'";
  $list = get_option($tempPic);
  if ($list === false) { $list = []; }
  $slide = [];
  $slide['pic'] = absint($_POST['image_attachment_id']);
  $slide['text'] = $_POST['text'];
  array_push($list, $slide);
  update_option($tempPic, $list);
}
?>
