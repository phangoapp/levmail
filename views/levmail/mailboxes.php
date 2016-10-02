<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaView\View;

function MailBoxesView($arr_domain, $list, $form_domains)
{

?>
    <p><a href="<?php echo AdminUtils::set_admin_link('levmail/servers', []); ?>"><?php echo I18n::lang('phangoapp/levmail', 'mail_servers', 'Mail servers'); ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('levmail/domains', ['server_id' => $arr_domain['server_IdServer']]); ?>"><?php echo $arr_domain['server_hostname']; ?></a></p>
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
    <input type="hidden" name="domain_id" value="<?php echo $arr_domain['IdDomainmail']; ?>" />
    <p><a href="<?php echo AdminUtils::set_admin_link('levmail/add_mailbox', ['domain_id' => $arr_domain['IdDomainmail']]); ?>"><?php echo I18n::lang('phangoapp/levmail', 'add_new_mailbox', 'Add new mailbox'); ?></a></p>
    <?php
    
    ob_start();

    ?>
    <script>
        
        $(document).ready( function () {
            
            domain_id=$('#domain_id').val();
            
            mailbox_hash={};
            
            $( ".mailbox_hash" ).each(function( index ) {
      
                mailbox_id=$(this).attr('id').replace('mailbox_name_', '');
      
                mailbox_hash[$(this).val()]=mailbox_id;
      
            });
            
            $.ajax({
                    url: "<?php echo AdminUtils::set_admin_link('levmail/getquotas', ['domain_id' => $arr_domain['IdDomainmail']]); ?>",
                    method: "GET",
                    dataType: "json",
                    }).done(function(data) {
                        
                        task_id=data['task_id'];
                        //Get info from progress 
                        
                        if(data['error']==0)
                        {
                         
                            get_quota();
                            
                        }
                        else
                        {
                            
                            alert('Error: cannot access to data server');
                            
                        }
                        
                    
                    }).fail(function (data) {
                            
                            alert(JSON.stringify(data));
                        
            });

        });
        
        function get_quota()
        {
                                
            $.ajax({
                url: "<?php echo AdminUtils::set_admin_link('leviathan/showprogress', ['op' => 1, 'server' => $arr_domain['ip']]); ?>/position/1/task_id/"+task_id,
                method: "GET",
                dataType: "json",
                data: {}
                }).done(function(data) {
                    
                    if(data.hasOwnProperty("wait"))
                    {
                        
                        setTimeout(get_quota, 1000);
                        
                    }
                    else
                    {
                        
                        mailbox=JSON.parse(data[0]['data']);
                        
                        for(x in mailbox)
                        {
                            
                            $('#mailbox_'+mailbox_hash[x]).html(mailbox[x]);
                            
                        }
                        
                    }
                    
                
                }).fail(function (data) {
                    
                        
                    alert(JSON.stringify(data));
                    
                });
            
        }
        
    </script>
    <?php

    View::$header[]=ob_get_contents();

    ob_end_clean();


}
else
{
    ?>
    <input type="hidden" name="domain_id" value="<?php echo $arr_domain['IdDomainmail']; ?>" />
    <?php
    
}

echo $list->show();



}
?>
