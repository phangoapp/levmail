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
        
        $this->name_task='Add autoreply to mailbox';
        
        $this->description_task='Add autoreply to mailbox';
        
        $this->codename_task='add_autoreply';
        
        $this->one_time=0;
        
        //$this->version=__DIR__.'/version';        
    }
    
    public function process_data()
    {
        
        $autoreply_redir=$this->data['mailbox'].'@autoreply.'.$this->data['hostname'];
        
        $tmp_dir='/tmp';
        
        $autoreply_file_subject=$tmp_dir.'/'.$this->data['mailbox'].'_autoreply_subject';
        $autoreply_file_text=$tmp_dir.'/'.$this->data['mailbox'].'_autoreply_text';
        
        $m=new Autoreply();
        
        $arr_autoreply=$m->where(['WHERE mailbox_id=?', [$this->data['mailbox_id']]])->select_a_row_where();
        
        file_put_contents($autoreply_file_subject, $arr_autoreply['subject']);
        
        file_put_contents($autoreply_file_text, $arr_autoreply['text']);
        
        $this->files[]=[$autoreply_file_subject, 0644];
        $this->files[]=[$autoreply_file_text, 0644];
        
        $this->commands_to_execute=[['sudo', 'vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/add_autoreply.py', '--mailbox "'.$this->data['mailbox'].'"']];
        
        if($this->data['first_time'])
        {
        
            $this->commands_to_execute[]=['sudo', 'vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/add_redirection.py', '--mailbox "'.$this->data['mailbox'].'" --redirection "'.$autoreply_redir.'"'];
        
        }
        
        return true;
        
    }
    
    public function post_task()
    {
        
        /*$m=new Autoreply();
        
        $m->create_forms();
        
        $m->reset_require();
        
        $m->insert($this->data);*/
        
        return true;
        
    }
    
    
}

?>
