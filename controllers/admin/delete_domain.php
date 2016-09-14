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

function Delete_domainAdmin()
{
    settype($_GET['domain_id'], 'integer');
    settype($_GET['confirmed'], 'integer');
    
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
    
        
    }
    
}

?>
