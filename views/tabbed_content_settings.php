<div id="icon-themes" class="icon32"><br></div>
<h2 class="nav-tab-wrapper">
    <?php
    $tabs = $this->container->getTabs();
    foreach ($tabs as $tab) {
        $active = ($tab->name == $this->container->getActive()->name) ? " nav-tab-active" : "";
        echo "<a class=\"nav-tab$active\" href=\"?$this->query_string&tab={$tab->name}\">{$tab->display}</a>";

    }
    ?>
</h2>
<?php $this->displayNotices(); ?>
<form method="post">
    <?php
    wp_nonce_field($this->nonce_action);
    $this->container->displayActive();
    ?>
    <br/>
    <?php
    //show update button
    submit_button();
    ?>
</form>
