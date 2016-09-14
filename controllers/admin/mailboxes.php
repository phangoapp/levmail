<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaView\View;
use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
Webmodel::load_model('vendor/phangoapp/levmail/models/mail');

function MailBoxesAdmin()
{
    settype($_GET['domain_id'], 'integer');
    
    $d=new DomainMail();
    
    $arr_domain=$d->select_a_row($_GET['domain_id']);
    
    //($name, $value, $model, $field_name, $field_value)
    $form_domains=new PhangoApp\PhaModels\Forms\SelectModelForm('domain_id', $_GET['domain_id'], $d, 'domain', 'IdDomainmail');
    
    $m=new MailBox();
    
    $list=new SimpleList($m, '');
    
    if($arr_domain)
    {
        
        $list->where_sql=['where domain_id=?', [$arr_domain['IdDomainmail']]];
        
    }
    
    $list->yes_search=1;
    
    $list->arr_fields_showed=['mailbox', 'quota'];
    
    $list->options_func='mailbox_options';
    
    echo View::load_view([$arr_domain, $list, $form_domains], 'levmail/mailboxes', 'phangoapp/levmail');

}

function mailbox_options($url_options, $model_name, $id, $arr_row)
{
    
    $arr_options=[];
    
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/config', ['mailbox_id' => $id]).'">'.I18n::lang('phangoapp/levmail', 'configuration', 'Configuration').'</a>';
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/autoreply', ['mailbox_id' => $id]).'">'.I18n::lang('phangoapp/levmail', 'autoreply', 'Autoreply').'</a>';
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/aliases', ['mailbox_id' => $id]).'">'.I18n::lang('phangoapp/levmail', 'aliases', 'Aliases').'</a>';
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/redirections', ['mailbox_id' => $id]).'">'.I18n::lang('phangoapp/levmail', 'redirections', 'Redirections').'</a>';
    //$arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/autoreply', ['mailbox_id' => $id]).'">'.I18n::lang('phangoapp/levmail', 'autoreply', 'Autoreply').'</a>';
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/change_quota_mailbox', ['mailbox_id' => $id]).'">'.I18n::lang('phangoapp/levmail', 'change_quota', 'Change quota').'</a>';
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/delete_mailbox', ['mailbox_id' => $id]).'">'.I18n::lang('common', 'delete', 'Delete').'</a>';
    
    return $arr_options;
    
}

?>
