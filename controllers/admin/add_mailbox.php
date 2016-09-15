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

function Add_MailboxAdmin()
{
    settype($_GET['domain_id'], 'integer');
    
    $d=new DomainMail();
    
    $arr_domain=$d->select_a_row($_GET['domain_id']);
    
    if($arr_domain)
    {
        
         ?>
        <p><a href="<?php echo AdminUtils::set_admin_link('levmail/servers', []); ?>"><?php echo I18n::lang('phangoapp/levmail', 'mail_servers', 'Mail servers'); ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('levmail/domains', ['server_id' => $arr_domain['server_IdServer']]); ?>"><?php echo $arr_domain['server_hostname']; ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('levmail/mailboxes', ['domain_id' => $arr_domain['IdDomainmail']]); ?>"><?php echo $arr_domain['domain']; ?></a></p>
        <?php
        
        $mailbox=new MailBox();
    
        $mailbox->create_forms(['mailbox', 'quota']);
        
        $max_quota=$arr_domain['quota']-get_max_quota($mailbox, $_GET['domain_id']);
        
        $mailbox->forms['mailbox']->comment_form='@'.$arr_domain['domain'];
        
        $mailbox->forms['quota']->comment_form=I18n::lang('phangoapp/levmail', 'megabytes', 'Megabytes').' - '.I18n::lang('phangoapp/levmail', 'maximum_quota', 'Maximum quota').': '.$max_quota.' '.I18n::lang('phangoapp/levmail', 'megabytes', 'Megabytes');
    
        if(PhangoApp\PhaRouter\Routes::$request_method!='POST')
        {
    
            $forms=ModelForm::show_form($mailbox->forms, [], $pass_values=false, $check_values=false);
    
            echo View::load_view([$arr_domain, $forms], 'levmail/add_mailbox', 'phangoapp/levmail');
        }
        else
        {
            
            $user=$_POST['mailbox'];
            
            $_POST['mailbox']=$_POST['mailbox'].'@'.$arr_domain['domain'];
            
            list($mailbox->forms, $post)=ModelForm::check_form($mailbox->forms, $_POST);
            
            if($post)
            {
                
                $q=0;
                
                if($post['quota']>$max_quota)
                {
                    
                    $mailbox->forms['quota']->required=true;
                    $mailbox->forms['quota']->std_error=I18n::lang('phangoapp/levmail', 'maximum:quota_overload', 'Maximum quota overload');
                    
                    $q=1;
                    
                }
                
                $c=$mailbox->where(['WHERE mailbox=?', [$post['mailbox']]])->select_count();
                
                if($c>0)
                {
                    
                    $mailbox->forms['mailbox']->std_error=I18n::lang('phangoapp/levmail', 'mailbox_exists', 'A mailbox with this name exists');
                    $mailbox->forms['mailbox']->default_value=$user;
                    
                }
                
                if($c==0 && $q==0)
                {
                
                    //Add task
                    $t=new Task();
                    
                    $post['ip']=$arr_domain['ip'];
                    $post['domain']=$arr_domain['domain'];
                    $post['group']=$arr_domain['group'];
                    $post['server']=$arr_domain['server'];
                    $post['domain_id']=$arr_domain['IdDomainmail'];
                    $post['user']=explode('@', $post['mailbox'])[0];
                    
                    $task_post=['name_task' => 'Add new mailbox - '.$post['domain'], 'description_task' => 'Add a new mailbox in a server', 'codename_task' => 'add_mailbox', 'data' => $post, 'path' => 'vendor/phangoapp/levmail/tasks/add_mailbox', 'hostname' => $arr_domain['server'], 'server' => $arr_domain['ip'], 'os_codename' => $arr_domain['server_os_codename']];
                    
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
                    
                    $forms=ModelForm::show_form($mailbox->forms, $_POST, $pass_values=true, $check_values=true);
    
                    echo View::load_view([$arr_domain, $forms], 'levmail/add_mailbox', 'phangoapp/levmail');
                    
                }
                
            }
            else
            {
                
                
    
                echo View::load_view([$arr_domain, $forms], 'levmail/add_mailbox', 'phangoapp/levmail');
                
            }
            
        }
    }
    
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
