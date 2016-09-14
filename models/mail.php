<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaModels\CoreFields;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');

class DomainMail extends Webmodel {
    
    
    public function load_components()
    {
        
        $this->register('domain', new CoreFields\DomainField(), true);
        $this->register('ip', new CoreFields\IpField(), true);
        $this->register('server', new CoreFields\ForeignKeyField(new Server(), $size=11, 0, $named_field="hostname", $select_fields=['ip', 'hostname', 'os_codename']));
        $this->register('group', new CoreFields\CharField(), true);
        $this->register('quota', new CoreFields\IntegerField());
        
    }
        
}

class MailBox extends Webmodel {
    
    
    public function load_components()
    {
        
        $this->register('mailbox', new CoreFields\EmailField(), true);
        $this->register('domain_id', new CoreFields\ForeignKeyField(new DomainMail(), $size=11, 0, $named_field="domain", $select_fields=['domain', 'quota', 'ip']));
        $this->register('quota', new CoreFields\IntegerField());
        
    }

}

class MailAlias extends Webmodel {
    
    
    public function load_components()
    {
        
        $this->register('alias', new CoreFields\EmailField(), true);
        $this->register('mailbox_id', new CoreFields\ForeignKeyField(new MailBox(), $size=11, 0, $named_field="mailbox", $select_fields=['IdMailbox']));
        
    }

}

class MailRedirection extends Webmodel {
    
    
    public function load_components()
    {
        
        $this->register('redirection', new CoreFields\EmailField(), true);
        $this->register('mailbox_id', new CoreFields\ForeignKeyField(new MailBox(), $size=11, 0, $named_field="mailbox", $select_fields=['IdMailbox']));
        
    }

}

class AutoReply extends Webmodel {
    
    
    public function load_components()
    {
        
        $this->register('subject', new CoreFields\CharField(), true);
        $this->register('text', new CoreFields\TextField(), true);
        $this->register('activation', new CoreFields\BooleanField());
        $this->register('mailbox_id', new CoreFields\ForeignKeyField(new MailBox(), $size=11, 0, $named_field="mailbox", $select_fields=['IdMailbox']));
        
    }

}


?>
