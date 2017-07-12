<table class="form-table">
    <tbody>
    <tr valign="top">
        <th width="35%" align="left">
            <label for="base_web_address">MarketPowerPRO Web Address</label>
        </th>
        <td align="left">
            <input style="width:400px;" type="url" name="base_web_address"
                   value="<?php echo $this->base_web_address; ?>"/>

            <p class="description">Enter your MarketPowerPRO web address (i.e. http://www.mymppedomain.com).</p>
        </td>
    </tr>
    <tr valign="top">
        <th width="35%" align="left">
            <label for="application_id">MarketPowerPRO Application ID</label>
        </th>
        <td align="left">
            <input style="width:400px;" type="text"
                   value="<?php echo $this->application_id; ?>"
                   name="application_id"/>

            <p class="description">Enter your application ID in this format:<br/> XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
            </p>
        </td>
    </tr>
    </tbody>
</table>
<?php
/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 5/28/2016
 * Time: 12:18 AM
 */