<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

function change_quota_mailboxView($arr_mailbox, $forms)
{

echo '<h3>'.I18n::lang('phangoapp/levmail', 'change_quota', 'Change quota').' - '.$arr_mailbox['mailbox'].'</h3>';

?>
<form method="post" action="<?php echo AdminUtils::set_admin_link('levmail/change_quota_mailbox', ['mailbox_id' => $arr_mailbox['IdMailbox']]); ?>">
<?php

echo $forms;

?>
<p><input type="submit" value="<?php echo I18n::lang('phangoapp/levmail', 'change_quota', 'Change quota'); ?>" /></p>
</form>
<?php

}

?>
