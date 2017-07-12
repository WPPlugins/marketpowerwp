<table class="form-table">
    <tbody>
    <tr valign="top">
        <th width="35%" align="left">
            <label for="replication_path">Replication Path</label>
        </th>
        <td align="left">
            <input type="text" name="replication_path" style="width:400px;"
                   value="<?php echo $this->replication_path; ?>"/>

            <p class="description">Path of MPP API to retrieve replication details. Set by default (No need to
                change).</p>
        </td>
    </tr>
    <tr valign="top">
        <th width="35%" align="left">
            <label for="replication_path_query_format">Replication Path Query Format</label>
        </th>
        <td align="left">
            <input type="text" name="replication_path_query_format" style="width:400px;"
                   value="<?php echo $this->replication_path_query_format; ?>"/>

            <p class="description">Query format used by replication path. Set by default (No need to change).</p>
        </td>
    </tr>
    <tr valign="top">
        <th width="35%" align="left">
            <label for="check_site_name_path">Check Site Name Path</label>
        </th>
        <td align="left">
            <input type="text" name="check_site_name_path" style="width:400px;"
                   value="<?php echo $this->check_site_name_path; ?>"/>

            <p class="description">Path in MPP API to check if entered replicated site name is valid. Set by default (No
                need to change).</p>
        </td>
    </tr>
    <tr valign="top">
        <th width="35%" align="left">
            <label for="check_site_name_path_query_format">Check Site Name Path Query Args</label>
        </th>
        <td align="left">
            <input type="text" name="check_site_name_path_query_format" style="width:400px;"
                   value="<?php echo $this->check_site_name_path_query_format; ?>"/>

            <p class="description">Query format of check site name path. Set by default (No
                need to change).</p>
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