<table class="form-table">
    <tbody>
    <tr valign="top">
        <th width="35%" align="left">
            <label for="nonexistent_site_redirection">Non-existent site name redirection page</label>
        </th>
        <td align="left">
            <?php wp_dropdown_pages($this->page_drop_down_args); ?>

            <p class="description">Empty by default and plugin will just send 404 page when a non-existent site
                name has been detected. You can optionally select a page to redirect to when this occurs.</p>
        </td>
    </tr>
    <tr valign="top">
        <th width="35%" align="left">
            <label for="autoprefix">Auto-prefix replication site name to URLs</label>
        </th>
        <td align="left">
            <input type="checkbox" name="autoprefix"<?php if ($this->autoprefix) { ?> checked="checked"<?php } ?>/>

            <p class="description"> Check if you want to auto-prefix your replication site name to all URLs in your
                wordpress site.
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