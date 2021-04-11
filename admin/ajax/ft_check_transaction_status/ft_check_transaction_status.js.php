<script type="text/javascript">
        var ft_ajaxurl='<?php echo admin_url('admin-ajax.php'); ?>';
      jQuery(function($){
            $("#refresh-status").on('click',$.debounce(500,event_process));
            function event_process(event){
                var wpnonce=encodeURIComponent($("[name='_wpnonce']").val());
                var wp_http_referer=encodeURIComponent($("[name='_wp_http_referer']").val());
        var order_id=encodeURIComponent($(this).data('order_id'));
        
        var post_data="order_id="+ order_id;
                post_data=post_data + "&_wpnonce="+ wpnonce;
                post_data=post_data + "&_wp_http_referer="+ wp_http_referer;
                
                var result_area=$('#status-result');//note if you change something here, please update $("#refresh-status").on('ft_check_transaction_status_received',function(evt,res){ as well
                ajax_call_ft_check_transaction_status(result_area,post_data);
                event.preventDefault();
            };

            function ajax_call_ft_check_transaction_status(ajax_ele,post_data){
                console.log('ft_ajaxurl:');
                console.log(ft_ajaxurl);
                $.ajax({
                    type:"POST",
                    data:post_data + "&action=c9wep_ft_check_transaction_status_ajax_admin_fun", 
                    url:ft_ajaxurl,
                    datatype:'json',
                    beforeSend:function(){
                            ft_check_transaction_status_updateTips(ajax_ele,'Checking...','ajax-going','color:#000');
                            },
                    success:function(res){
                        // console.log(res);
                        res=$.parseJSON(res);
                        if('success' == res.state){
                            ft_check_transaction_status_updateTips(ajax_ele,res.msg,'ajax-success','color:#0073AA');
                            var data=res.data;
                            $(ajax_ele).trigger('ft_check_transaction_status_received',res);
                    // location.reload();
                        }else{
                            ft_check_transaction_status_updateTips(ajax_ele,res.msg,'ajax-no-match','color:#f00');
                        }
                    },
                    error: function(res, textStatus, errorThrown){
                        // console.log(res);
                        ft_check_transaction_status_updateTips(ajax_ele,res.status + ' ' + res.statusText,'ajax-no-match','color:#f00');
                    }
                });
            }

            //please keep following selector as same as above: var result_area=$(this);//
            $("#status-result").on('ft_check_transaction_status_received',function(evt,res){
                console.log(res);
                console.log('res.data.redirect:');
                console.log(res.data.redirect);
                if(undefined !== res.data.redirect){
                    window.location.href=res.data.redirect;
                }
                // $(res.data).insertAfter(".ajax-loading");
            });

            function ft_check_transaction_status_updateTips(lead_ele,ele_msg,ele_class,ele_style){
                ele_tips=$(lead_ele).parent().find('.ft_check_transaction_status_msg-tips');
                if ($(ele_tips).length > 0) {
                    $(ele_tips).html(ele_msg);
                    $(ele_tips).attr('style',ele_style);
                }else{
                    $(lead_ele).after('<span class="ft_check_transaction_status_msg-tips" class="' + ele_class + '" style="'+ ele_style +'">' + ele_msg +'</span>');
                }
                // setTimeout(function() {
                //   $(ele_tips).html('&nbsp;');//to avoid jump
                // }, 5000);        
            }
      });
</script>

