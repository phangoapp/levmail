<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaModels\ModelForm;
use PhangoApp\PhaView\View;
use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\Leviathan\ConfigTask;
use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaI18n\I18n;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');
Webmodel::load_model('vendor/phangoapp/levmail/models/mail');

function Change_quota_mailboxAdmin()
{
    
    settype($_GET['mailbox_id'], 'integer');
    
    $m=new MailBox();
    
    $domain=new DomainMail();
    
    $arr_mailbox=$m->select_a_row($_GET['mailbox_id'], [], true);
    
    if($arr_mailbox) 
    {
        
        $arr_domain=$domain->select_a_row($arr_mailbox['domain_id']);
        
         ?>
        <p><a href="<?php echo AdminUtils::set_admin_link('levmail/servers', []); ?>"><?php echo I18n::lang('phangoapp/levmail', 'mail_servers', 'Mail servers'); ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('levmail/domains', ['server_id' => $arr_domain['server_IdServer']]); ?>"><?php echo $arr_domain['server_hostname']; ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('levmail/mailboxes', ['domain_id' => $arr_domain['IdDomainmail']]); ?>"><?php echo $arr_domain['domain']; ?></a></p>
        <?php
        
        $max_quota=$arr_domain['quota']-get_max_quota($m, $arr_mailbox['domain_id']);
        
        $m->create_forms(['quota']);
        
        $m->forms['quota']->default_value=$arr_mailbox['quota'];
                       
        $m->forms['quota']->comment_form=I18n::lang('phangoapp/levmail', 'megabytes', 'Megabytes').' - '.I18n::lang('phangoapp/levmail', 'maximum_quota', 'Maximum quota').': '.$max_quota.' '.I18n::lang('phangoapp/levmail', 'megabytes', 'Megabytes');

        
        if(PhangoApp\PhaRouter\Routes::$request_method!='POST')
        {
            
            $forms=ModelForm::show_form($m->forms, [], $pass_values=false, $check_values=false);
    
            echo View::load_view([$arr_mailbox, $forms], 'levmail/change_quota_mailbox', 'phangoapp/levmail');
        }
        else
        {
            
            
            list($m->forms, $post)=ModelForm::check_form($m->forms, $_POST);
            
            if($post['quota']>$max_quota)
            {
                
                $m->forms['quota']->required=true;
                $m->forms['quota']->std_error=I18n::lang('phangoapp/levmail', 'maximum:quota_overload', 'Maximum quota overload');
                
                $q=1;
                
                $post=false;
                
            }
            
            if($post)
            {
                //Add task
                $t=new Task();
                
                $post['mailbox_id']=$arr_mailbox['IdMailbox'];
                $post['user']=str_replace('@', '_', $arr_mailbox['mailbox']);
                
                $task_post=['name_task' => 'Change quota - '.$arr_mailbox['mailbox'], 'description_task' => 'Change quota of a mailbox in a server', 'codename_task' => 'change_quota_mailbox', 'data' => $post, 'path' => 'vendor/phangoapp/levmail/tasks/change_quota_mailbox', 'hostname' => $arr_domain['server'], 'server' => $arr_domain['ip'], 'os_codename' => $arr_domain['server_os_codename'], 'url_return' => AdminUtils::set_admin_link('levmail/change_quota_mailbox', ['mailbox_id' => $arr_mailbox['IdMailbox']])];
                
                $t->create_forms();
                
                if($t->insert($task_post))
                {
                    $id=$t->insert_id();
                    
                    $client=new GuzzleHttp\Client();
                                
                    $client->request('GET', ConfigTask::$url_server, [
                        'query' => ['task_id' => $id, 'api_key' => ConfigTask::$api_key]
                    ]);
                    
                    //http://localhost/leviathan/index.php/admin/leviathan/showprogress/get/task_id/201/server/192.168.2.5
                    
                    die(header('Location: '.AdminUtils::set_admin_link('leviathan/showprogress', ['task_id' => $id, 'server' => $arr_domain['ip']])));
                }
                
                //echo $t->std_error;

            }
            else
            {
                
                $forms=ModelForm::show_form($m->forms, $_POST, $pass_values=true, $check_values=true);
    
                echo View::load_view([$arr_mailbox, $forms], 'levmail/change_quota_mailbox', 'phangoapp/levmail');
                
            }
            
        }
        
    }
    
    /*
    if($arr_domain)
    {
        
        $arr_server=$s->select_a_row($arr_domain['server']);
        
        $domain->create_forms(['quota']);
    
        if(PhangoApp\PhaRouter\Routes::$request_method!='POST')
        {
            $m->forms['quota']->default_value=$arr_domain['quota'];
            
            $forms=ModelForm::show_form($m->forms, [], $pass_values=false, $check_values=false);
    
            echo View::load_view([$arr_server, $arr_domain, $forms], 'levmail/change_quota', 'phangoapp/levmail');
        }
        else
        {
            
            list($m->forms, $post)=ModelForm::check_form($m->forms, $_POST);
            
            if($post)
            {
                
                //Add task
                $t=new Task();
                
                $post['domain_id']=$arr_domain['IdDomainmail'];
                $post['domain']=$arr_domain['domain'];
                $post['group']=$arr_domain['group'];
                
                $task_post=['name_task' => 'Change quota - '.$arr_domain['domain'], 'description_task' => 'Change quota of a domain in a server', 'codename_task' => 'change_quota_domain', 'data' => $post, 'path' => 'vendor/phangoapp/levmail/tasks/change_quota', 'hostname' => $arr_server['hostname'], 'server' => $arr_server['ip'], 'os_codename' => $arr_server['os_codename']];
                
                $t->create_forms();
                
                if($t->insert($task_post))
                {
                    $id=$t->insert_id();
                    
                    $client=new GuzzleHttp\Client();
                                
                    $client->request('GET', ConfigTask::$url_server, [
                        'query' => ['task_id' => $id, 'api_key' => ConfigTask::$api_key]
                    ]);
                    
                    //http://localhost/leviathan/index.php/admin/leviathan/showprogress/get/task_id/201/server/192.168.2.5
                    
                    die(header('Location: '.AdminUtils::set_admin_link('leviathan/showprogress', ['task_id' => $id, 'server' => $arr_server['ip']])));
                }
                
                //echo $t->std_error;

            }
            else
            {
                
                $forms=ModelForm::show_form($m->forms, $_POST, $pass_values=true, $check_values=true);
    
                echo View::load_view([$arr_server, $arr_domain, $forms], 'levmail/change_quota', 'phangoapp/levmail');
                
            }
            
        }
    }*/
    
}

function get_max_quota($m, $domain_id)
{
    
    settype($domain_id, 'integer');
    
    $query=$m->query('select SUM(quota) from mailbox where domain_id='.$domain_id);
    
    list($total_quota)=$m->fetch_row($query);
    
    settype($total_quota, 'integer');
    
    return $total_quota;
    
}

?>
