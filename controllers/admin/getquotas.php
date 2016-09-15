<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaView\View;
use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaModels\Webmodel;
use PhangoApp\Leviathan\ConfigTask;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');
Webmodel::load_model('vendor/phangoapp/levmail/models/mail');

function GetQuotasAdmin()
{
    
    AdminUtils::$show_admin_view=false;

    settype($_GET['domain_id'], 'integer');

    $d=new DomainMail();

    $arr_domain=$d->select_a_row($_GET['domain_id']);
    
    if($arr_domain)
    {
    
        //Send request
        
        $t=new Task();
                    
        $post['ip']=$arr_domain['ip'];
        $post['domain']=$arr_domain['domain'];
        $post['group']=$arr_domain['group'];
        $post['server']=$arr_domain['server'];
        $post['domain_id']=$arr_domain['IdDomainmail'];
        
        $task_post=['name_task' => 'Get mailbox quotas - '.$post['domain'], 'description_task' => 'Add a new mailbox in a server', 'codename_task' => 'get_quotas_domain', 'data' => $post, 'path' => 'vendor/phangoapp/levmail/tasks/get_quotas', 'hostname' => $arr_domain['server'], 'server' => $arr_domain['ip'], 'os_codename' => $arr_domain['server_os_codename']];
        
        $t->create_forms();
        
        if($t->insert($task_post))
        {
            $id=$t->insert_id();
            
            $client=new GuzzleHttp\Client();
                        
            $client->request('GET', ConfigTask::$url_server, [
                'query' => ['task_id' => $id, 'api_key' => ConfigTask::$api_key]
            ]);
            
            //http://localhost/leviathan/index.php/admin/leviathan/showprogress/get/task_id/201/server/192.168.2.5
            
            header('Content-type: text/plain');

            die(json_encode(['error' => 0, 'message' => '', 'task_id' => $id]));
        }
        else
        {
            
            header('Content-type: text/plain');

            die(json_encode(['error' => 1, 'message' => 'Sorry, cannot connect to server', 'task' => 0]));
            
        }
    
        

    }
    
    header('Content-type: text/plain');

    die(json_encode(['error' => 1, 'message' => 'Domain not exists', 'task_id' => 0]));

}

?>
