<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

function RedirectionsView($arr_mailbox, $list)
{
    
    ?>
    <h3><?php echo I18n::lang('phangoapp/levmail', 'redirections', 'Redirections'); ?> - <?php echo $arr_mailbox['mailbox']; ?></h3>
    <p><a href="<?php echo AdminUtils::set_admin_link('levmail/redirections', ['op' => 1, 'mailbox_id' => $arr_mailbox['IdMailbox']]); ?>"><?php echo I18n::lang('phangoapp/levmail', 'add_redirections', 'Add redirections'); ?></a>
    <?php
    
    $list->show();
    
}

?>
