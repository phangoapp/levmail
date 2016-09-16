<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaModels\ModelForm;
use PhangoApp\PhaLibs\AdminUtils;

function change_password_mailboxView($arr_mailbox, $forms)
{   //http://localhost/leviathan/index.php/admin/levmail/config/get/mailbox_id/4
    ?>
    <h3><?php echo  I18n::lang('phangoapp/levmail', 'change_mailbox_password', 'Change mailbox password'); ?></h3>
    <form method="post" action="<?php echo AdminUtils::set_admin_link('levmail/config', ['op' => 1, 'mailbox_id' => $arr_mailbox['IdMailbox']]); ?>">
    <?php
                
    echo $forms;
    
    ?>
    <p><input type="submit" value="<?php echo  I18n::lang('phangoapp/levmail', 'change_mailbox_password', 'Change mailbox password'); ?>" />
    </form>
    <?php

}

?>
