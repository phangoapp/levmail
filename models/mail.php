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
        $this->register('domain_id', new CoreFields\ForeignKeyField(new DomainMail(), $size=11, 0, $named_field="domain", $select_fields=['domain', 'quota']));
        $this->register('quota', new CoreFields\IntegerField());
        
    }

}
?>
