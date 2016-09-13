<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

function add_domainView($arr_server, $forms)
{

echo '<h3>'.I18n::lang('phangoapp/levmail', 'add_new_domain', 'Add new domain').' - '.$arr_server['hostname'].'</h3>';

?>
<form method="post" action="<?php echo AdminUtils::set_admin_link('levmail/add_domain', ['server_id' => $arr_server['IdServer']]); ?>">
<?php

echo $forms;

?>
<p><input type="submit" value="<?php echo I18n::lang('phangoapp/levmail', 'add_new_domain', 'Add new domain'); ?>" /></p>
</form>
<?php

}

?>
