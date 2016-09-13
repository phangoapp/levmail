<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaView\View;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');

function ServersAdmin()
{
    
    $d=new DataServer();
    
    $servers=$d->where(['WHERE dataserver.ip IN (select ip from servergrouptask where name_task=?)', ['install_standalone_postfix']])->set_order(['server_id' => 0])->select_to_array();
 
    echo View::load_view([$servers], 'levmail/mailservers', 'phangoapp/levmail');
    
}

?>
