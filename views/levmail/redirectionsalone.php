<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

function RedirectionsAloneView($arr_domain, $list)
{
    
    ?>
    <h3><?php echo I18n::lang('phangoapp/levmail', 'redirections', 'Redirections'); ?> - <?php echo $arr_domain['domain']; ?></h3>
    <p><a href="<?php echo AdminUtils::set_admin_link('levmail/redirectionsalone', ['op' => 1, 'domain_id' => $arr_domain['IdDomainmail']]); ?>"><?php echo I18n::lang('phangoapp/levmail', 'add_redirections', 'Add redirections'); ?></a>
    <?php
    
    $list->show();
    
}

?>
