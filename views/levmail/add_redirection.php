<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

function Add_RedirectionView($arr_mailbox, $form)
{
    
    ?>
    <h3><?php echo I18n::lang('phangoapp/levmail', 'redirection', 'Redirection'); ?> - <?php echo $arr_mailbox['mailbox']; ?></h3>
    <h2><?php echo I18n::lang('phangoapp/levmail', 'add_redirection', 'Add redirection'); ?></h2>
    <form method="post" action="<?php echo AdminUtils::set_admin_link('levmail/redirections', ['mailbox_id' => $arr_mailbox['IdMailbox'], 'op' => 2]); ?>">
    <?php
    
    echo $form;
    ?>
    <p><input type="submit" value="<?php echo I18n::lang('phangoapp/levmail', 'add_redirection', 'Add redirection'); ?>" />
    </form>
    <?php
}

?>
