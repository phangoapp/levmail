<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaView\View;
use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaModels\ModelForm;
use PhangoApp\Leviathan\ConfigTask;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');
Webmodel::load_model('vendor/phangoapp/levmail/models/mail');

function AutoreplyAdmin()
{
    
    settype($_GET['op'], 'integer');
    settype($_GET['mailbox_id'], 'integer');
    
    $m=new MailBox();
    
    $a=new AutoReply();
    
    $d=new DomainMail();
    
    $arr_mailbox=$m->select_a_row($_GET['mailbox_id'], [], true);
    
    echo '<h2>'.$arr_mailbox['mailbox'].'</h2>';
    
    if($arr_mailbox)
    {
        
        $arr_domain=$d->select_a_row($arr_mailbox['domain_id']);
        
        ?>
        <p><a href="<?php echo AdminUtils::set_admin_link('levmail/servers', []); ?>"><?php echo I18n::lang('phangoapp/levmail', 'mail_servers', 'Mail servers'); ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('levmail/domains', ['server_id' => $arr_domain['server_IdServer']]); ?>"><?php echo $arr_domain['server_hostname']; ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('levmail/mailboxes', ['domain_id' => $arr_domain['IdDomainmail']]); ?>"><?php echo $arr_domain['domain']; ?></a></p>
        <?php
        
        $arr_reply=$a->where(['WHERE mailbox_id=?', [$arr_mailbox['IdMailbox']]])->select_a_row_where();
        
        $reply=[];
                
        if($arr_reply)
        {
            
            $reply=$arr_reply;
            
        }
        else
        {
            
            $arr_reply['activation']=0;
            
        }
        
        $a->create_forms(['subject', 'text', 'activation']);
                
        $a->forms['activation']=new PhangoApp\PhaModels\Forms\SelectForm('activation', $arr_reply['activation'], $arr_select=[0 => I18n::lang('common', 'no', 'No'), 1 => I18n::lang('common', 'yes', 'Yes')]);
        
        $a->forms['activation']->label=I18n::lang('phangoapp\levmail', 'activation', 'Activate autoreply');
        
        $url_post=AdminUtils::set_admin_link('levmail\autoreply', ['op' => 1, 'mailbox_id' => $arr_mailbox['IdMailbox']]);
        
        switch($_GET['op'])
        {
            
            default:
                
                
                $forms=ModelForm::show_form($a->forms, $reply, $pass_values=true, $check_values=false);
                
                ?>
                <form method="post" action="<?php echo $url_post; ?>">
                <?php
                
                echo $forms;
                
                ?>
                
                <p><input type="submit" value="<?php echo I18n::lang('phangoapp/levmail', 'send_autoreply', 'Send autoreply'); ?>" /></p>
                </form>
                <?php
                
            break;
            
            case 1:
            
                list($a->forms, $post)=ModelForm::check_form($a->forms, $_POST);
                
                if($post)
                {
                    
                    $post['mailbox_id']=$arr_mailbox['IdMailbox'];
                    
                    //Update or insert
                    
                    $insert='insert';
                    
                    if($a->where(['WHERE mailbox_id=?', [$arr_mailbox['IdMailbox']]])->select_count()>0)
                    {
                        
                        $insert='update';
                        
                        $a->set_conditions(['WHERE mailbox_id=?', [$arr_mailbox['IdMailbox']]]);
                        
                    }
                    
                    $a->fields_to_update[]='mailbox_id';
                    
                    if(!$a->$insert($post))
                    {
                        
                        
                        echo 'Error: cannot update or insert the autoreply';
                        
                    }
                    else
                    {
                        
                        if(!$post['activation'])
                        {
                        
                            //Need disable in server
                            
                            if($post['activation']!=$arr_reply['activation'])
                            {
                                
                                //Disable message
                                
                                $post['mailbox_id']=$arr_mailbox['IdMailbox'];
                                $post['mailbox']=$arr_mailbox['mailbox'];
                                $post['hostname']=$arr_domain['server_hostname'];
                                
                                $task_post=['name_task' => 'Add autoreply - '.$arr_mailbox['mailbox'], 'description_task' => 'Delete autoreply of mailbox', 'codename_task' => 'delete_autoreply', 'data' => $post, 'path' => 'vendor/phangoapp/levmail/tasks/delete_autoreply', 'hostname' => $arr_domain['server_hostname'], 'server' => $arr_domain['ip'], 'os_codename' => $arr_domain['server_os_codename'], 'url_return' => AdminUtils::set_admin_link('levmail/mailboxes', ['domain_id' => $arr_domain['IdDomainmail']])];
                                
                                $t=new Task();
                                
                                $t->create_forms();
                                
                                if($t->insert($task_post))
                                {
                                    $id=$t->insert_id();
                                    
                                    $client=new GuzzleHttp\Client();
                                                
                                    $client->request('GET', ConfigTask::$url_server, [
                                        'query' => ['task_id' => $id, 'api_key' => ConfigTask::$api_key]
                                    ]);
                                    
                                    
                                    die(header('Location: '.AdminUtils::set_admin_link('leviathan/showprogress', ['task_id' => $id, 'server' => $arr_domain['ip']])));
                                }
                                
                            }
                            else
                            {
                                View::set_flash('Updated autoreply for '.$arr_mailbox['mailbox'].' sucessfully but not activated');
                                
                                die(header('Location: '.AdminUtils::set_admin_link('levmail/mailboxes', ['domain_id' => $arr_mailbox['domain_id']])));
                                
                            }
                            
                        }
                        else
                        {
                            
                            $post['first_time']=0;
                            
                            if($post['activation']!=$arr_reply['activation'])
                            {
                             
                                $post['first_time']=1;
                                
                            }
                            
                            $post['mailbox_id']=$arr_mailbox['IdMailbox'];
                            $post['mailbox']=$arr_mailbox['mailbox'];
                            $post['hostname']=$arr_domain['server_hostname'];
                            
                            $task_post=['name_task' => 'Add autoreply - '.$arr_mailbox['mailbox'], 'description_task' => 'Add autoreply to mailbox', 'codename_task' => 'add_autoreply', 'data' => $post, 'path' => 'vendor/phangoapp/levmail/tasks/add_autoreply', 'hostname' => $arr_domain['server_hostname'], 'server' => $arr_domain['ip'], 'os_codename' => $arr_domain['server_os_codename'], 'url_return' => AdminUtils::set_admin_link('levmail/mailboxes', ['domain_id' => $arr_domain['IdDomainmail']])];
                            
                            $t=new Task();
                            
                            $t->create_forms();
                            
                            if($t->insert($task_post))
                            {
                                $id=$t->insert_id();
                                
                                $client=new GuzzleHttp\Client();
                                            
                                $client->request('GET', ConfigTask::$url_server, [
                                    'query' => ['task_id' => $id, 'api_key' => ConfigTask::$api_key]
                                ]);
                                
                                
                                die(header('Location: '.AdminUtils::set_admin_link('leviathan/showprogress', ['task_id' => $id, 'server' => $arr_domain['ip']])));
                            }
                            
                        }
                    }
                    
                }
                else
                {
                    
                    $forms=ModelForm::show_form($a->forms, $_POST, $pass_values=true, $check_values=true);
                
                    ?>
                    <form method="post" action="<?php echo $url_post; ?>">
                    <?php
                    
                    echo $forms;
                    
                    ?>
                    
                    <p><input type="submit" value="<?php echo I18n::lang('phangoapp/levmail', 'send_autoreply', 'Send autoreply'); ?>" /></p>
                    </form>
                    <?php
                    
                    
                }
            
            break;
            
        }
    
    }
    

}

?>
