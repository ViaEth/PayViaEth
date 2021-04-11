<script type="text/javascript">
        var ajaxurl='<?php echo admin_url('admin-ajax.php'); ?>';
      jQuery(function($){
        $("#update-transaction").on('click',$.debounce(500,event_process));
            function event_process(event){
                var wpnonce=encodeURIComponent($("[name='_wpnonce']").val());
                var wp_http_referer=encodeURIComponent($("[name='_wp_http_referer']").val());
        var order_id=encodeURIComponent($("[name='order_id']").val());
        var ether_amount=encodeURIComponent($("[name='ether_amount']").val());
        var ether_transaction_status=encodeURIComponent($("[name='ether_transaction_status']").val());
        var ether_transaction_init=encodeURIComponent($("[name='ether_transaction_init']").val());
        var ether_transaction_confirm=encodeURIComponent($("[name='ether_transaction_confirm']").val());
        
        var post_data="ether_transaction_status="+ ether_transaction_status;
            post_data=post_data + "&order_id="+ order_id;
            post_data=post_data + "&ether_amount="+ ether_amount;
            post_data=post_data + "&ether_transaction_init="+ ether_transaction_init;
            post_data=post_data + "&ether_transaction_confirm="+ ether_transaction_confirm;
        post_data=post_data + "&_wpnonce="+ wpnonce;
        post_data=post_data + "&_wp_http_referer="+ wp_http_referer;
        
        var result_area=$(this);//note if you change something here, please update $("#update-transaction").on('update_transaction_status_received',function(evt,res){ as well
        ajax_call_update_transaction_status(result_area,post_data);
        event.preventDefault();
    };
    function ajax_call_update_transaction_status(ajax_ele,post_data){
        $.ajax({
            type:"POST",
            data:post_data + "&action=c9wep_update_transaction_status_ajax_admin_fun", 
            url:ajaxurl,
            datatype:'json',
            beforeSend:function(){
                    update_transaction_status_updateTips(ajax_ele,'Loading...','ajax-going','color:#000');
                    },
            success:function(res){
                // console.log(res);
                res=$.parseJSON(res);
                if('success' == res.state){
                    update_transaction_status_updateTips(ajax_ele,res.msg,'ajax-success','color:#0073AA');
                    var data=res.data;
                    $(ajax_ele).trigger('update_transaction_status_received',res);
                    // window.location.reload();
                }else{
                    update_transaction_status_updateTips(ajax_ele,res.msg,'ajax-no-match','color:#f00');
                }
            },
            error: function(res, textStatus, errorThrown){
                // console.log(res);
                update_transaction_status_updateTips(ajax_ele,res.status + ' ' + res.statusText,'ajax-no-match','color:#f00');
            }
        });
    }

            //please keep following selector as same as above: var result_area=$(this);//
            $("#update-transaction").on('update_transaction_status_received',function(evt,res){
                // console.log(res);
                // $(res.data).insertAfter(".ajax-loading");
            });

            function update_transaction_status_updateTips(lead_ele,ele_msg,ele_class,ele_style){
                ele_tips=$(lead_ele).parent().find('.update_transaction_status_msg-tips');
                if ($(ele_tips).length > 0) {
                    $(ele_tips).html(ele_msg);
                    $(ele_tips).attr('style',ele_style);
                }else{
                    $(lead_ele).after('<span class="update_transaction_status_msg-tips" class="' + ele_class + '" style="'+ ele_style +'">' + ele_msg +'</span>');
                }
                // setTimeout(function() {
                //   $(ele_tips).html('&nbsp;');//to avoid jump
                // }, 5000);        
            }
      });
</script>

