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
        
        $this->name_task='Add redirection to mailbox';
        
        $this->description_task='Add redirection to mailbox';
        
        $this->codename_task='add_redirection';
        
        $this->one_time=0;
        
        //$this->version=__DIR__.'/version';        
    }
    
    public function process_data()
    {
        
        $this->commands_to_execute=[['sudo', 'vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/add_redirection.py', '--mailbox "'.$this->data['mailbox'].'" --redirection "'.$this->data['redirection'].'"']];
        
        return true;
        
    }
    
    public function post_task()
    {
        
        $m=new MailRedirection();
        
        $m->create_forms();
        
        $m->reset_require();
        
        $m->insert($this->data);
        
        return true;
        
    }
    
    
}

?>
