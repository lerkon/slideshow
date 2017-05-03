<?php
  /*
    Plugin name: Slideshow_Widget
    Version: 123
  */

  class Awesome_Widget extends WP_Widget
  {

    public function __construct()
    {
      $title = 'Slideshow';
      $id = 'awesome_widget';
      $args = [
        'description' => 'My awesome slideshow',
        'classname' => 'awesome_widget',
      ];
      parent::__construct($id, $title, $args);
    }

    public function widget($args, $instance)
    {
      wp_register_script('jquery.slides', plugins_url( '/jquery.slides.js', __FILE__ ));
      wp_enqueue_script('jquery.slides');
      wp_register_style('slideshowFrame', plugins_url( '/playing/main.css', __FILE__ ));
      wp_enqueue_style('slideshowFrame');
      global $wpdb;
      $post = $wpdb->get_results($wpdb->prepare(
        "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='slideshow'",
        sanitize_text_field($instance['post_name'])
      ));
      $list = get_post_meta($post[0]->ID, 'slideshowPost', true);
      ?>
        <aside id="recent-posts-2" class="widget widget_recent_entries">
          <h2 class="widget-title"><?php echo $instance['title']; ?></h2>
            <div id="slides">
              <?php
                foreach ($list as $key) {
                  $src = wp_get_attachment_url($key['pic']);
                  echo "<img id='image-preview' height='100' src='{$src}'>";
                }
              ?>
            </div>
    		</aside>
        <script type="text/javascript">
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
        </script>
      <?php

    }

    public function form($instance)
    {
      $title = (!empty($instance['title'])? $instance['title'] : 'New title');
      $post_name = (!empty($instance['post_name'])? $instance['post_name'] : 'post');
      ?>
        <div>
          <p>Title</p>
          <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>">
        </div>
        <div>
          <p>Post name</p>
          <input type="text" name="<?php echo $this->get_field_name('post_name'); ?>" value="<?php echo $post_name; ?>">
        </div>
      <?php
    }

    public function update($new_instance, $old_instance)
    {
      $instance = [];
      $instance['title'] = $new_instance['title'];
      $instance['post_name'] = $new_instance['post_name'];
      return $instance;
    }

  }

  function wpb_load_widget() {
      register_widget( 'Awesome_Widget' );
  }
  add_action( 'widgets_init', 'wpb_load_widget' );
?>
