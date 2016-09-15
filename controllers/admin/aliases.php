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

function AliasesAdmin()
{
    settype($_GET['mailbox_id'], 'integer');
    settype($_GET['op'], 'integer');
    
    $m=new MailBox();
    
    $arr_mailbox=$m->select_a_row($_GET['mailbox_id'], [], true);
    
    if($arr_mailbox)
    {
    
        $d=new DomainMail();
    
        $arr_domain=$d->select_a_row($arr_mailbox['domain_id']);
        
        $a=new MailAlias();
        
        $a->create_forms(['alias']);
        
        $a->forms['alias']->comment_form='@'.$arr_domain['domain'];
        
        ?>
        <p><a href="<?php echo AdminUtils::set_admin_link('levmail/servers', []); ?>"><?php echo I18n::lang('phangoapp/levmail', 'mail_servers', 'Mail servers'); ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('levmail/domains', ['server_id' => $arr_domain['server_IdServer']]); ?>"><?php echo $arr_domain['server_hostname']; ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('levmail/mailboxes', ['domain_id' => $arr_domain['IdDomainmail']]); ?>"><?php echo $arr_domain['domain']; ?></a></p>
        <?php
        
        switch($_GET['op'])
        {
            default:
        
                $list=new SimpleList($a, '');
                
                $list->yes_search=1;
                
                $list->arr_fields_showed=['alias'];
                
                $list->options_func='alias_options';
                
                echo View::load_view([$arr_mailbox, $list], 'levmail/aliases', 'phangoapp/levmail');

            break;
            
            case 1:
            
                $form=ModelForm::show_form($a->forms, [], $pass_values=false, $check_values=false);
            
                echo View::load_view([$arr_mailbox, $form], 'levmail/add_alias', 'phangoapp/levmail');
            
            break;
            
            case 2:
            
                $_POST['alias']=$_POST['alias'].'@'.$arr_domain['domain'];
            
                list($a->forms, $post)=ModelForm::check_form($a->forms, $_POST);
                
                if($post)
                {
                    
                    $c=$a->where(['WHERE alias=?', [$post['alias']]])->select_count();
                    
                    if($c==0)
                    {
                    
                        $post['mailbox_id']=$arr_mailbox['IdMailbox'];
                        $post['mailbox']=$arr_mailbox['mailbox'];
                        
                        $task_post=['name_task' => 'Add alias - '.$arr_mailbox['mailbox'], 'description_task' => 'Add alias to mailbox', 'codename_task' => 'add_alias', 'data' => $post, 'path' => 'vendor/phangoapp/levmail/tasks/add_alias', 'hostname' => $arr_domain['server'], 'server' => $arr_domain['ip'], 'os_codename' => $arr_domain['server_os_codename']];
                        
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
                        
                        
                        $form=ModelForm::show_form($a->forms, $_POST, $pass_values=true, $check_values=true);
            
                        echo View::load_view([$arr_mailbox, $form], 'levmail/add_alias', 'phangoapp/levmail');
                        
                    }
                    
                    
                }
                else
                {
                    
                    $a->forms['alias']->std_error=I18n::lang('phangoapp\levmail', 'error_alias_exists', 'Exista an alias with this name');
                    
                    $form=ModelForm::show_form($a->forms, $_POST, $pass_values=true, $check_values=true);
            
                    echo View::load_view([$arr_mailbox, $form], 'levmail/add_alias', 'phangoapp/levmail');
                    
                }
            
            break;
            
            case 3:
            
                settype($_GET['confirmed'], 'integer');
                settype($_GET['alias_id'], 'integer');
                
                $arr_alias=$a->select_a_row($_GET['alias_id']);
                
                if($arr_alias)
                {
                    
                    switch($_GET['confirmed'])
                    {
                        
                        default:
                        
                            ?>
                            <h2><?php echo $arr_alias['alias']; ?></h2>
                            <div class="form">
                                <input type="button" id="delete_alias" name="delete_domain" value="<?php echo I18n::lang('phangoapp/levmail', 'you_are_sure_delete_alias', 'Are you sure for delete alias?'); ?>" />
                                <script>
                                    $('#delete_alias').click( function () {
                                        
                                        location.href='<?php echo AdminUtils::set_admin_link('levmail/aliases', ['alias_id' => $_GET['alias_id'], 'mailbox_id' => $_GET['mailbox_id'], 'op' => 3, 'confirmed' => 1]); ?>';
                                        
                                    });
                                </script>
                            </div>
                            <?php
                        
                        break;
                        
                        case 1:
                            
                
                            $post['alias']=$arr_alias['alias'];
                            
                            $task_post=['name_task' => 'Delete alias - '.$arr_alias['alias'], 'description_task' => 'Delete alias of mailbox', 'codename_task' => 'delete_alias', 'data' => $post, 'path' => 'vendor/phangoapp/levmail/tasks/delete_alias', 'hostname' => $arr_domain['server'], 'server' => $arr_domain['ip'], 'os_codename' => $arr_domain['server_os_codename']];
                            
                            
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

function alias_options($url_options, $model_name, $id, $arr_row)
{
    
    $arr_options=[];
    
    /*$arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/aliases', ['mailbox_id' => $id]).'">'.I18n::lang('phangoapp/levmail', 'aliases', 'Aliases').'</a>';
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/autoreply', ['mailbox_id' => $id]).'">'.I18n::lang('phangoapp/levmail', 'autoreply', 'Autoreply').'</a>';
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/change_quota_mailbox', ['mailbox_id' => $id]).'">'.I18n::lang('phangoapp/levmail', 'change_quota', 'Change quota').'</a>';*/
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('levmail/aliases', ['alias_id' => $id, 'mailbox_id' => $_GET['mailbox_id'], 'op' => 3]).'">'.I18n::lang('common', 'delete', 'Delete').'</a>';
    
    return $arr_options;
    
}

?>
