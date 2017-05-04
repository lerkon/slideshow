<div>
  <div class='form-control'>
    <label>
      Width<br>
      <input type='number' name='width' value='<?php echo $width; ?>'/>
    </label><br>
    <label>Height<br>
      <input type='number' name='height' value='<?php echo $height; ?>'/>
    </label><br>
    <label>Interval<br>
      <input type='number' name='interval' value='<?php echo $interval; ?>'/>
    </label>
    <hr>
    <form method='post'>
      <label>Text<br>
        <input type='text' name='text'/>
      </label>
      <div class='image-preview-wrapper'>
        <img id='image-preview' height='auto' style="max-height:100px;">
      </div>
      <input id="upload_image_button" type="button" class="button" value="Upload image" />
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
