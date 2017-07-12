<div class="wrap">
    <h1>Replication Shortcodes</h1>

    <p> This is a plugin that communicates with the MarketPowerPRO web services API to be used in your Wordpress
        site. The plugin generates shortcodes that you can use in building post/pages in your Wordpress site.</p>

    <div class="postbox" style="width:50%;padding:10px;">
        <h3>Example</h3>
        <strong>Here's an example:</strong>
        <br/>
        <?php echo $this->sample; ?>
        <br/><br/>
        <strong>Output would be:</strong>
        <br/>
        <?php echo $this->parsed_sample; ?>
        <br/><br/>
    </div>
    <div class="postbox" style="width:50%;padding:10px;">
        <h3>Available shortcodes</h3>
        <ul style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
            <li>
                MPPE_FIRSTNAME
            </li>

            <li>
                MPPE_LASTNAME
            </li>

            <li>
                MPPE_BIRTHDAY
            </li>

            <li>
                MPPE_EMAIL
            </li>

            <li>
                MPPE_ADDRESS1
            </li>

            <li>
                MPPE_ADDRESS2
            </li>

            <li>
                MPPE_CITY
            </li>

            <li>
                MPPE_COUNTY
            </li>

            <li>
                MPPE_POSTALCODE
            </li>

            <li>
                MPPE_HOMEPHONE
            </li>

            <li>
                MPPE_BUSINESSPHONE
            </li>

            <li>
                MPPE_CELL
            </li>

            <li>
                MPPE_FAX
            </li>

            <li>
                MPPE_REGIONNAME
            </li>

            <li>
                MPPE_COUNTRYNAME
            </li>

            <li>
                MPPE_SITENAME
            </li>

            <li>
                MPPE_WEBSITE
            </li>
        </ul>
    </div>
<?php
/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 6/5/2016
 * Time: 11:44 AM
 */