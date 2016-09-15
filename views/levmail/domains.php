<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

function DomainsView($arr_server, $list)
{

echo '<h2>'.I18n::lang('phangoapp/levmail', 'mail_servers', 'Mail servers').'</h2>';

if($arr_server)
{

?>
<p><a href="<?php echo AdminUtils::set_admin_link('levmail/servers', []); ?>"><?php echo I18n::lang('phangoapp/levmail', 'mail_servers', 'Mail servers'); ?></a> &gt;&gt; <?php echo $arr_server['hostname']; ?></p>
<?php

echo '<h3>'.$arr_server['hostname'].'</h3>';

?>
<p><a href="<?php echo AdminUtils::set_admin_link('levmail/add_domain', ['server_id' => $arr_server['IdServer']]); ?>"><?php echo I18n::lang('phangoapp/levmail', 'add_new_domain', 'Add new domain'); ?></a></p>
<?php
}

echo $list->show();

}

?>
