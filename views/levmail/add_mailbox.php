<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

function add_mailboxView($arr_domain, $forms)
{

echo '<h3>'.I18n::lang('phangoapp/levmail', 'add_new_mailbox', 'Add new mailbox').' - '.$arr_domain['domain'].'</h3>';

?>
<form method="post" action="<?php echo AdminUtils::set_admin_link('levmail/add_mailbox', ['domain_id' => $arr_domain['IdDomainmail']]); ?>">
<?php

echo $forms;

?>
<p><input type="submit" value="<?php echo I18n::lang('phangoapp/levmail', 'add_new_mailbox', 'Add new mailbox'); ?>" /></p>
</form>
<?php

}

?>
