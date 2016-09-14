<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaView\View;
use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaModels\ModelForm;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
Webmodel::load_model('vendor/phangoapp/levmail/models/mail');

function AutoreplyAdmin()
{
    settype($_GET['mailbox_id'], 'integer');
    
    $m=new MailBox();
    
    $a=new AutoReply();
    
    $arr_mailbox=$m->select_a_row($_GET['mailbox_id']);
    
    echo '<h2>'.$arr_mailbox['mailbox'].'</h2>';
    
    $a->create_forms(['subject', 'text']);
    
    $forms=ModelForm::show_form($a->forms, [], $pass_values=false, $check_values=false);
    
    ?>
    <form method="post" action="">
    <?php
    
    echo $forms;
    
    ?>
    
    <p><input type="submit" value="<?php echo I18n::lang('phangoapp/levmail', 'send_autoreply', 'Send autoreply'); ?>" /></p>
    </form>
    <?php
    
    

}

?>
