<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

function Add_AliasView($arr_mailbox, $form)
{
    
    ?>
    <h3><?php echo I18n::lang('phangoapp/levmail', 'alias', 'Alias'); ?> - <?php echo $arr_mailbox['mailbox']; ?></h3>
    <h2><?php echo I18n::lang('phangoapp/levmail', 'add_alias', 'Add alias'); ?></h2>
    <form method="post" action="<?php echo AdminUtils::set_admin_link('levmail/aliases', ['mailbox_id' => $arr_mailbox['IdMailbox'], 'op' => 2]); ?>">
    <?php
    
    echo $form;
    ?>
    <p><input type="submit" value="<?php echo I18n::lang('phangoapp/levmail', 'add_alias', 'Add alias'); ?>" />
    </form>
    <?php
}

?>
