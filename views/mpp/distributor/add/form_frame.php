<?php if ($this->app_info): ?>
    <p style="text-align: center;">
        This form allows you to add distributors to your MarketPowerPRO deployment. Embed
        this form to your pages by using the shortcode:
        <br/><strong>[mppe-add-distibutor-form][/mppe-add-distibutor-form]</strong>
    </p>
<?php endif; ?>
    <iframe src="<?php echo $this->app_src; ?>"
            style="display:block;text-align:center;width:<?php echo $this->app_width; ?>;height:<?php echo $this->app_height; ?>;min-height: 630px;margin:auto;"
            allowTransparency="true"
            frameborder="0"
            allowfullscreen="false"
            scrolling="yes">
    </iframe>
<?php
/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 6/23/2016
 * Time: 1:49 AM
 */