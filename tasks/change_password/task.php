<?php

use PhangoApp\Leviathan\Task;
use PhangoApp\PhaModels\Forms;
use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaModels\ModelForm;
use PhangoApp\PhaI18n\I18n;

Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');
Webmodel::load_model('vendor/phangoapp/levmail/models/mail');

class ServerTask extends Task {
    
    public function define()
    {
        
        #THe files to delete
        
        $this->name_task='Change password of mailbox';
        
        $this->description_task='Change password of mailbox in a postfix mail server';
        
        $this->codename_task='change_password';
        
        $this->one_time=0;
        
        //$this->version=__DIR__.'/version';        
    }
    
    public function process_data()
    {
        
        $this->commands_to_execute=[['sudo', 'vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/change_password.py', '--user "'.$this->data['user'].'" --password "'.$this->data['password'].'"']];
        
        return true;
        
    }
    
    public function post_task()
    {
        
        
        
    }
    
    
}

?>
