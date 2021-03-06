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

function Change_quotaAdmin()
{
    settype($_GET['domain_id'], 'integer');
    
    $s=new Server();
    
    $domain=new DomainMail();
    
    $arr_domain=$domain->select_a_row($_GET['domain_id'], [], true);
    
    if($arr_domain)
    {
        
        $arr_server=$s->select_a_row($arr_domain['server']);
        
        ?>
        <p><a href="<?php echo AdminUtils::set_admin_link('levmail/servers', []); ?>"><?php echo I18n::lang('phangoapp/levmail', 'mail_servers', 'Mail servers'); ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('levmail/domains', ['server_id' => $arr_server['IdServer']]); ?>"><?php echo $arr_server['hostname']; ?></a></p>
        <?php
        
        $domain->create_forms(['quota']);
    
        if(PhangoApp\PhaRouter\Routes::$request_method!='POST')
        {
            $domain->forms['quota']->default_value=$arr_domain['quota'];
            
            $forms=ModelForm::show_form($domain->forms, [], $pass_values=false, $check_values=false);
    
            echo View::load_view([$arr_domain, $forms], 'levmail/change_quota', 'phangoapp/levmail');
        }
        else
        {
            
            list($domain->forms, $post)=ModelForm::check_form($domain->forms, $_POST);
            
            if($post)
            {
                
                //Add task
                $t=new Task();
                
                $post['domain_id']=$arr_domain['IdDomainmail'];
                $post['domain']=$arr_domain['domain'];
                $post['group']=$arr_domain['group'];
                
                $task_post=['name_task' => 'Change quota - '.$arr_domain['domain'], 'description_task' => 'Change quota of a domain in a server', 'codename_task' => 'change_quota_domain', 'data' => $post, 'path' => 'vendor/phangoapp/levmail/tasks/change_quota', 'hostname' => $arr_server['hostname'], 'server' => $arr_server['ip'], 'os_codename' => $arr_server['os_codename'], 'url_return' => AdminUtils::set_admin_link('levmail/change_quota', ['domain_id' => $arr_domain['IdDomainmail']])];
                
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
                
                $forms=ModelForm::show_form($domain->forms, $_POST, $pass_values=true, $check_values=true);
    
                echo View::load_view([$arr_domain, $forms], 'levmail/change_quota', 'phangoapp/levmail');
                
            }
            
        }
    }
    
}

?>
