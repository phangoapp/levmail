<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaModels\ModelForm;
use PhangoApp\PhaView\View;
use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\Leviathan\ConfigTask;
use PhangoApp\PhaLibs\AdminUtils;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');
Webmodel::load_model('vendor/phangoapp/levmail/models/mail');

function Add_DomainAdmin()
{
    settype($_GET['server_id'], 'integer');
    
    $s=new Server();
    
    $arr_server=$s->select_a_row($_GET['server_id']);
    
    if($arr_server)
    {
        
        $domain=new DomainMail();
    
        $domain->create_forms(['domain', 'group', 'quota']);
    
        if(PhangoApp\PhaRouter\Routes::$request_method!='POST')
        {
    
            $forms=ModelForm::show_form($domain->forms, [], $pass_values=false, $check_values=false);
    
            echo View::load_view([$arr_server, $forms], 'levmail/add_domain', 'phangoapp/levmail');
        }
        else
        {
            
            list($domain->forms, $post)=ModelForm::check_form($domain->forms, $_POST);
            
            if($post)
            {
                
                $c=$domain->where(['WHERE domain=?', [$post['domain']]])->select_count();
                
                if($c==0)
                {
                
                    //Add task
                    $t=new Task();
                    
                    $post['ip']=$arr_server['ip'];
                    $post['server']=$arr_server['IdServer'];
                    
                    $task_post=['name_task' => 'Add new domain - '.$post['domain'], 'description_task' => 'Add a new domain in a server', 'codename_task' => 'add_domain', 'data' => $post, 'path' => 'vendor/phangoapp/levmail/tasks/add_domain', 'hostname' => $arr_server['hostname'], 'server' => $arr_server['ip'], 'os_codename' => $arr_server['os_codename']];
                    
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
                    
                    $domain->forms['domain']->std_error='Error: mailbox exists in database';
                    
                    $forms=ModelForm::show_form($domain->forms, $_POST, $pass_values=true, $check_values=true);
    
                    echo View::load_view([$arr_server, $forms], 'levmail/add_domain', 'phangoapp/levmail');
                    
                }
                
            }
            else
            {
                
                $forms=ModelForm::show_form($domain->forms, $_POST, $pass_values=true, $check_values=true);
    
                echo View::load_view([$arr_server, $forms], 'levmail/add_domain', 'phangoapp/levmail');
                
            }
            
        }
    }
    
    /*
    $d=new DataServer();
    
    $servers=$d->where(['WHERE dataserver.ip IN (select ip from servergrouptask where name_task=?)', ['install_standalone_postfix']])->set_order(['server_id' => 0])->select_to_array();
 
    echo View::load_view([$servers], 'levmail/mailservers', 'phangoapp/levmail');
    */
}

?>
