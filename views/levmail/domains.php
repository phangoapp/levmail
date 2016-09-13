<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

function DomainsView($arr_server, $list)
{

echo '<h3>'.$arr_server['hostname'].'</h3>';

?>
<p><a href="<?php echo AdminUtils::set_admin_link('levmail/add_domain', ['server_id' => $arr_server['IdServer']]); ?>"><?php echo I18n::lang('phangoapp/levmail', 'add_new_domain', 'Add new domain'); ?></a></p>
<?php

echo $list->show();

}

?>
