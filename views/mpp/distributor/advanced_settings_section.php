<table class="form-table">
    <tbody>
    <tr valign="top">
        <th width="35%" align="left">
            <label for="replication_path">Get Countries Path</label>
        </th>
        <td align="left">
            <input type="text" name="get_countries_path" style="width:400px;"
                   value="<?php echo $this->get_countries_path; ?>"/>

            <p class="description">Path to get available MPP countries for distributors.</p>
        </td>
    </tr>
    <tr valign="top">
        <th width="35%" align="left">
            <label for="replication_path_query_format">Get Regions Path</label>
        </th>
        <td align="left">
            <input type="text" name="get_regions_path" style="width:400px;"
                   value="<?php echo $this->get_regions_path; ?>"/>

            <p class="description">Path to get available MPP regions for distributors.</p>
        </td>
    </tr>
    <tr valign="top">
        <th width="35%" align="left">
            <label for="check_site_name_path">Get Commission Payment Path</label>
        </th>
        <td align="left">
            <input type="text" name="get_commission_payment_method_path" style="width:400px;"
                   value="<?php echo $this->get_commission_payment_method_path; ?>"/>

            <p class="description">Path to get available MPP commission payment methods for distributors.</p>
        </td>
    </tr>
    <tr valign="top">
        <th width="35%" align="left">
            <label for="check_site_name_path_query_format">Get Binary Side List Path</label>
        </th>
        <td align="left">
            <input type="text" name="get_binary_side_list_path" style="width:400px;"
                   value="<?php echo $this->get_binary_side_list_path; ?>"/>

            <p class="description">Path to get available MPP binary side lists for distributors.</p>
        </td>
    </tr>
    </tbody>
</table>
<?php
/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 6/17/2016
 * Time: 3:36 PM
 */