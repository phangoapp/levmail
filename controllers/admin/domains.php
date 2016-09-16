<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaView\View;
use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
Webmodel::load_model('vendor/phangoapp/levmail/models/mail');

function DomainsAdmin()
{
    settype($_GET['server_id'], 'integer');
    
    $s=new Server();
    
    $arr_server=$s->select_a_row($_GET['server_id']);
    
    $m=new DomainMail();
    
    $list=new SimpleList($m);
    
    $list->url_options=AdminUtils::set_admin_link('levmail/domains', ['server_id' => $arr_server['IdServer']]);
    
    $list->yes_search=1;
    
    $list->arr_fields_showed=['domain', 'quota'];
    
    $list->options_func='domain_options';
    
    echo View::load_view([$arr_server, $list], 'levmail/domains', 'phangoapp/levmail');

}

function domain_options($url_options, $model_name, $id, $arr_row)
{
    
    $arr_options=[];
    
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/mailboxes', ['domain_id' => $id]).'">'.I18n::lang('phangoapp/levmail', 'mailboxes', 'Mailboxes').'</a>';
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/change_quota', ['domain_id' => $id]).'">'.I18n::lang('phangoapp/levmail', 'change_quota', 'Change quota').'</a>';
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/delete_domain', ['domain_id' => $id]).'">'.I18n::lang('common', 'delete', 'Delete').'</a>';
    
    return $arr_options;
    
}

?>
