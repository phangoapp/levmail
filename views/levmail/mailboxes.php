<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

function MailBoxesView($arr_domain, $list, $form_domains)
{

?>
    <p>
    <?php
    echo $form_domains->form();
    ?>
    </p>
    <script>
        $('#domain_id_field_form').change( function () {
           
            location.href='<?php echo AdminUtils::set_admin_link('levmail/mailboxes'); ?>/get/domain_id/'+$(this).val();
            
        });
    </script>
    <?php

if($arr_domain)
{   

    ?>
    <p><a href="<?php echo AdminUtils::set_admin_link('levmail/add_mailbox', ['domain_id' => $arr_domain['IdDomainmail']]); ?>"><?php echo I18n::lang('phangoapp/levmail', 'add_new_mailbox', 'Add new mailbox'); ?></a></p>
    <?php

}

echo $list->show();

}

?>
