<?php

use PhangoApp\PhaUtils\SimpleTable;
use PhangoApp\PhaUtils\Size;
use PhangoApp\PhaI18n\I18n;

function MailServersView($servers)
{
    
    echo '<h2>'.I18n::lang('phangoapp/levmail', 'healthy_mail_servers', 'Healthy of mail servers').'</h2>';
    
    foreach($servers as $server)
    {
        $arr_disk=[];
        
        echo '<h3>'.$server['server_id'].'</h3>';
        
        SimpleTable::top_table_config([I18n::lang('phangoapp/leviathan', 'disk', 'Disk'), I18n::lang('phangoapp/leviathan', 'free', 'Free')], $cell_sizes=array());
        
        for($x=0;$x<6;$x++)
        {
            
            $arr_disk[$server['disk'.$x.'_id']]=$server['disk'.$x.'_id_free'];
            
        }
        
        foreach($arr_disk as $disk => $free)
        {
            
            echo SimpleTable::middle_table_config([$disk, Size::format($free)], $cell_sizes=array());
            
        }
        
        SimpleTable::header_middle_table_config([I18n::lang('phangoapp/leviathan', 'cpu_number', 'Cpu number'), I18n::lang('phangoapp/leviathan', 'total_cpu_idle', 'Total cpu idle')], $cell_sizes=array());
        
        echo SimpleTable::middle_table_config([$server['cpu_id_num_cpu'], $server['cpu_id']], $cell_sizes=array());
        
        SimpleTable::header_middle_table_config([I18n::lang('phangoapp/leviathan', 'memory_used', 'Memory used'), I18n::lang('common', 'options', 'Options')], $cell_sizes=array());
        
        $url_domains='<a href="'.PhangoApp\PhaLibs\AdminUtils::set_admin_link('levmail/domains', ['server_id' => $server['server_id_IdServer']]).'">'.I18n::lang('phangoapp\leviathan', 'domains', 'Domains').'</a>';
        
        $memory_usage='<strong>'.Size::format($server['memory_id_free']).' '.I18n::lang('phangoapp/leviathan', 'free', 'free').'</strong> | '.Size::format($server['memory_id_used']).' '.I18n::lang('phangoapp/leviathan', 'used', 'used').' | '.Size::format($server['memory_id_cached']).' '.I18n::lang('phangoapp/leviathan', 'cached', 'cached');
        
        echo SimpleTable::middle_table_config([$memory_usage, $url_domains], $cell_sizes=array());
        
        SimpleTable::bottom_table_config();
    }

}

?>
