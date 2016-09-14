<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

function AliasesView($arr_mailbox, $list)
{
    
    ?>
    <h3><?php echo I18n::lang('phangoapp/levmail', 'alias', 'Alias'); ?> - <?php echo $arr_mailbox['mailbox']; ?></h3>
    <p><a href="<?php echo AdminUtils::set_admin_link('levmail/aliases', ['op' => 1, 'mailbox_id' => $arr_mailbox['IdMailbox']]); ?>"><?php echo I18n::lang('phangoapp/levmail', 'add_alias', 'Add alias'); ?></a>
    <?php
    
    $list->show();
    
}

?>
