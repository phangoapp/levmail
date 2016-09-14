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
        
        $this->name_task='Delete mailbox';
        
        $this->description_task='Delete mailbox of a domain in a server';
        
        $this->codename_task='delete_mailbox';
        
        $this->one_time=0;
        
        //$this->version=__DIR__.'/version';        
    }
    
    public function process_data()
    {
        
        $this->commands_to_execute=[['sudo', 'vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/remove_user.py', '--mailbox "'.$this->data['mailbox'].'"']];
        
        return true;
        
    }
    
    public function post_task()
    {
        
        $m=new MailBox();
        
        $m->where(['WHERE IdMailbox=?', [$this->data['mailbox_id']]])->delete();
        
        return true;
        
    }
    
    
}

?>
