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

function Delete_mailboxAdmin()
{
    settype($_GET['mailbox_id'], 'integer');
    settype($_GET['confirmed'], 'integer');
    
    $m=new MailBox();
    
    $arr_mailbox=$m->select_a_row($_GET['mailbox_id'], [], true);
    
    if($arr_mailbox)
    {
        
        $domain=new DomainMail();
    
        $arr_domain=$domain->select_a_row($arr_mailbox['domain_id']);
        
         ?>
        <p><a href="<?php echo AdminUtils::set_admin_link('levmail/servers', []); ?>"><?php echo I18n::lang('phangoapp/levmail', 'mail_servers', 'Mail servers'); ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('levmail/domains', ['server_id' => $arr_domain['server_IdServer']]); ?>"><?php echo $arr_domain['server_hostname']; ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('levmail/mailboxes', ['domain_id' => $arr_domain['IdDomainmail']]); ?>"><?php echo $arr_domain['domain']; ?></a></p>
        <?php
        
        switch($_GET['confirmed'])
        {
            
            default:
            
                ?>
                <h2><?php echo $arr_domain['domain']; ?> - <?php echo $arr_mailbox['mailbox']; ?></h2>
                <div class="form">
                    <input type="button" id="delete_mailbox" name="delete_mailbox" value="<?php echo I18n::lang('phangoapp/levmail', 'you_are_sure_delete', 'Are you sure for delete mailbox?'); ?>" />
                    <script>
                        $('#delete_mailbox').click( function () {
                            
                            location.href='<?php echo AdminUtils::set_admin_link('levmail/delete_mailbox', ['mailbox_id' => $arr_mailbox['IdMailbox'], 'confirmed' => 1]); ?>';
                            
                        });
                    </script>
                </div>
                <?php
            
            break;
            
            case 1:
                
                $t=new Task();
                
                $post['mailbox']=$arr_mailbox['mailbox'];
                $post['mailbox_id']=$arr_mailbox['IdMailbox'];
                
                $task_post=['name_task' => 'Delete  mailbox - '.$arr_domain['domain'], 'description_task' => 'Delete mailbox', 'codename_task' => 'delete_mailbox', 'data' => $post, 'path' => 'vendor/phangoapp/levmail/tasks/delete_mailbox', 'hostname' => $arr_domain['server'], 'server' => $arr_domain['server_ip'], 'os_codename' => $arr_domain['server_os_codename']];
                
                $t->create_forms();
                
                if($t->insert($task_post))
                {
                    $id=$t->insert_id();
                    
                    $client=new GuzzleHttp\Client();
                                
                    $client->request('GET', ConfigTask::$url_server, [
                        'query' => ['task_id' => $id, 'api_key' => ConfigTask::$api_key]
                    ]);
                    
                    die(header('Location: '.AdminUtils::set_admin_link('leviathan/showprogress', ['task_id' => $id, 'server' => $arr_domain['server_ip']])));
                }
            
            break;
            
        }
        
        
    }
    
    /*
    
    $s=new Server();
    
    $domain=new DomainMail();
    
    $arr_domain=$domain->select_a_row($_GET['domain_id'], [], true);
    
    if($arr_domain)
    {
        
        $arr_server=$s->select_a_row($arr_domain['server']);
        
        switch($_GET['confirmed'])
        {
            
            default:
            
                ?>
                <h2><?php echo $arr_server['hostname']; ?> - <?php echo $arr_domain['domain']; ?></h2>
                <div class="form">
                    <input type="button" id="delete_domain" name="delete_domain" value="<?php echo I18n::lang('phangoapp/levmail', 'you_are_sure_delete', 'Are you sure for delete domain?'); ?>" />
                    <script>
                        $('#delete_domain').click( function () {
                            
                            location.href='<?php echo AdminUtils::set_admin_link('levmail/delete_domain', ['domain_id' => $arr_domain['IdDomainmail'], 'confirmed' => 1]); ?>';
                            
                        });
                    </script>
                </div>
                <?php
            
            break;
            
            case 1:
            
                $t=new Task();
                
                $post['domain']=$arr_domain['domain'];
                $post['domain_id']=$arr_domain['IdDomainmail'];
                $post['group']=$arr_domain['group'];
                
                $task_post=['name_task' => 'Delete  domain - '.$arr_domain['domain'], 'description_task' => 'Delete domain', 'codename_task' => 'delete_domain', 'data' => $post, 'path' => 'vendor/phangoapp/levmail/tasks/delete_domain', 'hostname' => $arr_server['hostname'], 'server' => $arr_server['ip'], 'os_codename' => $arr_server['os_codename']];
                
                $t->create_forms();
                
                if($t->insert($task_post))
                {
                    $id=$t->insert_id();
                    
                    $client=new GuzzleHttp\Client();
                                
                    $client->request('GET', ConfigTask::$url_server, [
                        'query' => ['task_id' => $id, 'api_key' => ConfigTask::$api_key]
                    ]);
                    
                    die(header('Location: '.AdminUtils::set_admin_link('leviathan/showprogress', ['task_id' => $id, 'server' => $arr_server['ip']])));
                }
            
            break;
            
        }
    
        
    }*/
    
}

?>
