<form id="multisoft_mpp_adf_form_config"
      action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
      method="post">
    <?php wp_nonce_field($this->form_action); ?>
    <input type="hidden" name="form_config" id="multisoft_mpp_adf_form_config_value"/>
    <input type="hidden" name="action" value="<?php echo $this->form_action; ?>"/>
</form>
<iframe src="<?php echo $this->app_src; ?>"
        style="display:block;width:98%;height:auto;margin-top:5px;min-height:630px;"
        allowTransparency="true"
        frameborder="0"
        allowfullscreen="false"
        scrolling="no">
</iframe>

<?php
/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 6/23/2016
 * Time: 1:49 AM
 */