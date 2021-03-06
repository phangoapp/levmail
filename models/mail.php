<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaModels\CoreFields;
use PhangoApp\PhaI18n\I18n;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');

class QuotaField extends CoreFields\IntegerField {
    
    public function show_formatted($value)
    {
        
        settype($value, 'float');
        
        $return_text=I18n::lang('phangoapp\levmail', 'unlimited', 'Unlimited');
        
        if($value>0)
        {
        
            $return_text=$value.' Mb';
            
        }
        
        return $return_text;
        
    }
    
}


class DomainMail extends Webmodel {
    
    
    public function load_components()
    {
        
        $this->register('domain', new CoreFields\DomainField(), true);
        $this->register('ip', new CoreFields\IpField(), true);
        $this->register('alias_server', new CoreFields\DomainField());
        $this->register('server', new CoreFields\ForeignKeyField(new Server(), $size=11, 0, $named_field="hostname", $select_fields=['ip', 'hostname', 'os_codename', 'IdServer']));
        $this->register('group', new CoreFields\CharField(), true);
        $this->register('quota', new QuotaField());
        
    }
        
}

class MailBox extends Webmodel {
    
    
    public function load_components()
    {
        
        $this->register('mailbox', new CoreFields\EmailField(), true);
        $this->register('domain_id', new CoreFields\ForeignKeyField(new DomainMail(), $size=11, 0, $named_field="domain", $select_fields=['domain', 'quota', 'ip']));
        $this->register('quota', new QuotaField());
        
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

class MailRedirectionDomain extends Webmodel {
    
    
    public function load_components()
    {
        
        $this->register('mailbox', new CoreFields\EmailField(), true);
        $this->register('redirection', new CoreFields\EmailField(), true);
        
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
