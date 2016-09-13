<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

function change_quotaView($arr_server, $arr_domain, $forms)
{

echo '<h3>'.I18n::lang('phangoapp/levmail', 'change_quota', 'Change quota').' - '.$arr_domain['domain'].'</h3>';

?>
<form method="post" action="<?php echo AdminUtils::set_admin_link('levmail/change_quota', ['domain_id' => $arr_domain['IdDomainmail']]); ?>">
<?php

echo $forms;

?>
<p><input type="submit" value="<?php echo I18n::lang('phangoapp/levmail', 'change_quota', 'Change quota'); ?>" /></p>
</form>
<?php

}

?>
