<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaView\View;
use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
Webmodel::load_model('vendor/phangoapp/levmail/models/mail');

function AutoreplyAdmin()
{
    settype($_GET['mailbox_id'], 'integer');
    
    $m=new MailBox();
    
    $arr_mailbox=$m->select_a_row($_GET['mailbox_id']);
    
    echo '<h2>'.$arr_mailbox['mailbox'].'</2>';
    
    $arr_mailbox->create_forms();
    

}

?>
