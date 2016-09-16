<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaModels\ModelForm;
use PhangoApp\PhaView\View;
use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\Leviathan\ConfigTask;

Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');
Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
Webmodel::load_model('vendor/phangoapp/levmail/models/mail');

function ConfigAdmin()
{
    settype($_GET['mailbox_id'], 'integer');
    settype($_GET['op'], 'integer');
    
    $m=new MailBox();
    
    $arr_mailbox=$m->select_a_row($_GET['mailbox_id'], [], true);
    
    if($arr_mailbox)
    {
    
        $d=new DomainMail();
    
        $arr_domain=$d->select_a_row($arr_mailbox['domain_id']);
        
        $forms=[];
                
        $forms['password']=new PhangoApp\PhaModels\Forms\PasswordForm('password', '');
        $forms['password']->required=true;
        $forms['password']->label=I18n::lang('common', 'password', 'Password');
        $forms['repeat_password']=new PhangoApp\PhaModels\Forms\PasswordForm('repeat_password', '');
        $forms['repeat_password']->label=I18n::lang('common', 'repeat_password', 'Repeat password');
        $forms['repeat_password']->required=true;
        
        ?>
        <p><a href="<?php echo AdminUtils::set_admin_link('levmail/servers', []); ?>"><?php echo I18n::lang('phangoapp/levmail', 'mail_servers', 'Mail servers'); ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('levmail/domains', ['server_id' => $arr_domain['server_IdServer']]); ?>"><?php echo $arr_domain['server_hostname']; ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('levmail/mailboxes', ['domain_id' => $arr_domain['IdDomainmail']]); ?>"><?php echo $arr_domain['domain']; ?></a>
        &gt;&gt; <?php echo $arr_mailbox['mailbox']; ?></p>
        <?php
        
        switch($_GET['op'])
        {
            default:
        
                $smtp_server=$arr_domain['alias_server'];
        
                if(!$arr_domain['alias_server'])
                {
                
                    $smtp_server=$arr_domain['server_hostname'];
                    
                }
        
                ?>
                <p>
                    <?php echo I18n::lang('phangoapp/levmail', 'smtp_server', 'SMTP server'); ?>: <?php echo $smtp_server; ?>
                </p>
                <p>
                    <?php echo I18n::lang('phangoapp/levmail', 'imap_server', 'IMAP server'); ?>: <?php echo $smtp_server; ?>
                </p>
                <p>
                    <?php echo I18n::lang('phangoapp/levmail', 'pop3_server', 'POP3 server'); ?>: <?php echo $smtp_server; ?>
                </p>
                <p>
                    <?php echo I18n::lang('phangoapp/levmail', 'username', 'Username'); ?>: <?php echo str_replace('@', '_', $arr_mailbox['mailbox']); ?>
                </p>
                
                <?php
                
                $forms=ModelForm::show_form($forms, [], $pass_values=false, $check_values=false);
                
                echo View::load_view([$arr_mailbox, $forms], 'levmail/change_password_mailbox', 'phangoapp/levmail');
            
            break;
            
            case 1:
            
                list($forms, $post)=ModelForm::check_form($forms, $_POST);
                
                if(!$post)
                {
                
                    $forms=ModelForm::show_form($forms, $_POST, $pass_values=true, $check_values=true);
                
                    echo View::load_view([$arr_mailbox, $forms], 'levmail/change_password_mailbox', 'phangoapp/levmail');
                
                }
                else
                {
                
                    $p=0;
                
                    if(trim($_POST['password'])!==trim($_POST['repeat_password']))
                    {
                    
                        $forms['password']->std_error=I18n::lang('phangoapp/levmail', 'password_not_match', 'Passwords doesnt match');
                        
                        $p=1;
                    
                    }
                    
                    if($p==0)
                    {
                        
                        $post['user']=str_replace('@', '_', $arr_mailbox['mailbox']);
                        $post['password']=$_POST['password'];
                        
                        unset($post['repeat_password']);
                        
                        $task_post=['name_task' => 'Change password - '.$arr_mailbox['mailbox'], 'description_task' => 'Change password of mailbox', 'codename_task' => 'change_password_mailbox', 'data' => $post, 'path' => 'vendor/phangoapp/levmail/tasks/change_password', 'hostname' => $arr_domain['server'], 'server' => $arr_domain['ip'], 'os_codename' => $arr_domain['server_os_codename']];
                    
                        $t=new Task();
                    
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

                    
                    }
                    else
                    {
                    
                        $forms=ModelForm::show_form($forms, $_POST, $pass_values=true, $check_values=true);
                
                        echo View::load_view([$arr_mailbox, $forms], 'levmail/change_password_mailbox', 'phangoapp/levmail');
                    
                    }
                    
                
                }
            
            break;

        }

    }

}

?>
