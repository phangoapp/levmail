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

function RedirectionsAdmin()
{
    settype($_GET['mailbox_id'], 'integer');
    settype($_GET['op'], 'integer');
    
    $m=new MailBox();
    
    $arr_mailbox=$m->select_a_row($_GET['mailbox_id'], [], true);
    
    if($arr_mailbox)
    {
    
        $d=new DomainMail();
    
        $arr_domain=$d->select_a_row($arr_mailbox['domain_id']);
        
        $r=new MailRedirection();
        
        $r->create_forms(['redirection']);
        
        ?>
        <p><a href="<?php echo AdminUtils::set_admin_link('levmail/servers', []); ?>"><?php echo I18n::lang('phangoapp/levmail', 'mail_servers', 'Mail servers'); ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('levmail/domains', ['server_id' => $arr_domain['server_IdServer']]); ?>"><?php echo $arr_domain['server_hostname']; ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('levmail/mailboxes', ['domain_id' => $arr_domain['IdDomainmail']]); ?>"><?php echo $arr_domain['domain']; ?></a></p>
        <?php
        
        //$r->forms['redirection']->comment_form='@'.$arr_domain['domain'];
        
        switch($_GET['op'])
        {
            default:
        
                $list=new SimpleList($r, '');
                
                $list->yes_search=1;
                
                $list->arr_fields_showed=['redirection'];
                
                $list->options_func='redirection_options';
                
                $list->where_sql=['WHERE mailbox_id=?', [$arr_mailbox['IdMailbox']]];
                
                echo View::load_view([$arr_mailbox, $list], 'levmail/redirections', 'phangoapp/levmail');

            break;
            
            case 1:
            
                $form=ModelForm::show_form($r->forms, [], $pass_values=false, $check_values=false);
            
                echo View::load_view([$arr_mailbox, $form], 'levmail/add_redirection', 'phangoapp/levmail');
            
            break;
            
            case 2:
            
                //$_POST['redirection']=$_POST['redirection'].'@'.$arr_domain['domain'];
            
                list($r->forms, $post)=ModelForm::check_form($r->forms, $_POST);
                
                if($post)
                {
                    
                    $c=$r->where(['WHERE redirection=?', [$post['redirection']]])->select_count();
                    
                    if($c==0)
                    {
                    
                        $post['mailbox_id']=$arr_mailbox['IdMailbox'];
                        $post['mailbox']=$arr_mailbox['mailbox'];
                        
                        $task_post=['name_task' => 'Add redirection - '.$arr_mailbox['mailbox'], 'description_task' => 'Add redirection to mailbox', 'codename_task' => 'add_redirection', 'data' => $post, 'path' => 'vendor/phangoapp/levmail/tasks/add_redirections', 'hostname' => $arr_domain['server'], 'server' => $arr_domain['ip'], 'os_codename' => $arr_domain['server_os_codename'], 'url_return' => AdminUtils::set_admin_link('levmail/redirections', ['mailbox_id' => $arr_mailbox['IdMailbox']])];
                        
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
                        
                        
                        $form=ModelForm::show_form($r->forms, $_POST, $pass_values=true, $check_values=true);
            
                        echo View::load_view([$arr_mailbox, $form], 'levmail/add_redirection', 'phangoapp/levmail');
                        
                    }
                    
                    
                }
                else
                {
                    
                    $r->forms['redirection']->std_error=I18n::lang('phangoapp/levmail', 'error_redirection_exists', 'Exista an redirection with this name');
                    
                    $form=ModelForm::show_form($r->forms, $_POST, $pass_values=true, $check_values=true);
            
                    echo View::load_view([$arr_mailbox, $form], 'levmail/add_redirection', 'phangoapp/levmail');
                    
                }
            
            break;
            
            case 3:
            
                settype($_GET['confirmed'], 'integer');
                settype($_GET['redirection_id'], 'integer');
                
                $arr_redirection=$r->select_a_row($_GET['redirection_id']);
                
                if($arr_redirection)
                {
                    
                    switch($_GET['confirmed'])
                    {
                        
                        default:
                        
                            ?>
                            <h2><?php echo $arr_redirection['redirection']; ?></h2>
                            <div class="form">
                                <input type="button" id="delete_redirection" name="delete_domain" value="<?php echo I18n::lang('phangoapp/levmail', 'you_are_sure_delete_redirection', 'Are you sure for delete redirection?'); ?>" />
                                <script>
                                    $('#delete_redirection').click( function () {
                                        
                                        location.href='<?php echo AdminUtils::set_admin_link('levmail/redirections', ['redirection_id' => $_GET['redirection_id'], 'mailbox_id' => $_GET['mailbox_id'], 'op' => 3, 'confirmed' => 1]); ?>';
                                        
                                    });
                                </script>
                            </div>
                            <?php
                        
                        break;
                        
                        case 1:
                            
                
                            $post['redirection']=$arr_redirection['redirection'];
                            $post['mailbox']=$arr_mailbox['mailbox'];
                            
                            $task_post=['name_task' => 'Delete redirection - '.$arr_redirection['redirection'], 'description_task' => 'Delete redirection of mailbox', 'codename_task' => 'delete_redirection', 'data' => $post, 'path' => 'vendor/phangoapp/levmail/tasks/delete_redirection', 'hostname' => $arr_domain['server'], 'server' => $arr_domain['ip'], 'os_codename' => $arr_domain['server_os_codename'], 'url_return' => AdminUtils::set_admin_link('levmail/redirections', ['mailbox_id' => $arr_mailbox['IdMailbox']])];
                            
                            
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
                            
                        
                        break;
                        
                    }
            
                }
            
            break;

        }

    }

}

function redirection_options($url_options, $model_name, $id, $arr_row)
{
    
    $arr_options=[];
    
    /*$arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/redirections', ['mailbox_id' => $id]).'">'.I18n::lang('phangoapp/levmail', 'redirections', 'Aliases').'</a>';
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/autoreply', ['mailbox_id' => $id]).'">'.I18n::lang('phangoapp/levmail', 'autoreply', 'Autoreply').'</a>';
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/change_quota_mailbox', ['mailbox_id' => $id]).'">'.I18n::lang('phangoapp/levmail', 'change_quota', 'Change quota').'</a>';*/
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/redirections', ['redirection_id' => $id, 'mailbox_id' => $_GET['mailbox_id'], 'op' => 3]).'">'.I18n::lang('common', 'delete', 'Delete').'</a>';
    
    return $arr_options;
    
}

?>
