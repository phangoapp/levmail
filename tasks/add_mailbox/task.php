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
        
        $this->name_task='Add new mailbox';
        
        $this->description_task='Add a new mailbox to Postifx mail server';
        
        $this->codename_task='add_mailbox';
        
        $this->one_time=0;
        
        //$this->version=__DIR__.'/version';        
    }
    
    public function process_data()
    {
        
        $this->commands_to_execute=[['sudo', 'vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/add_user.py', '--domain "'.$this->data['domain'].'" --user "'.$this->data['user'].'" --quota "'.$this->data['quota'].'"']];
        
        return true;
        
    }
    
    public function post_task()
    {
        
        $m=new MailBox();
        
        $m->create_forms();
        
        $m->insert($this->data);
        
        return true;
        
    }
    
    
}

?>
