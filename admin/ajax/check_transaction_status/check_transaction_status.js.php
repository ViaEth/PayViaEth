<script type="text/javascript">
        var ajaxurl='<?php echo admin_url('admin-ajax.php'); ?>';
      jQuery(function($){
            $("#check_transaction_status").on('click',$.debounce(500,event_process));
            function event_process(event){
                var wpnonce=encodeURIComponent($("[name='_wpnonce']").val());
                var wp_http_referer=encodeURIComponent($("[name='_wp_http_referer']").val());
        var cronjob=encodeURIComponent($("[name='cronjob']").val());
        
        var post_data="cronjob="+ cronjob;
                post_data=post_data + "&_wpnonce="+ wpnonce;
                post_data=post_data + "&_wp_http_referer="+ wp_http_referer;
                
                var result_area=$(this);//note if you change something here, please update $("#check_transaction_status").on('check_transaction_status_received',function(evt,res){ as well
                ajax_call_check_transaction_status(result_area,post_data);
                event.preventDefault();
            };

            function ajax_call_check_transaction_status(ajax_ele,post_data){
                $.ajax({
                    type:"POST",
                    data:post_data + "&action=c9wep_check_transaction_status_ajax_admin_fun", 
                    url:ajaxurl,
                    datatype:'json',
                    beforeSend:function(){
                            check_transaction_status_updateTips(ajax_ele,'Loading...','ajax-going','color:#000');
                            },
                    success:function(res){
                        // console.log(res);
                        res=$.parseJSON(res);
                        if('success' == res.state){
                            check_transaction_status_updateTips(ajax_ele,res.msg,'ajax-success','color:#0073AA');
                            var data=res.data;
                            $(ajax_ele).trigger('check_transaction_status_received',res);
                    // location.reload();
                        }else{
                            check_transaction_status_updateTips(ajax_ele,res.msg,'ajax-no-match','color:#f00');
                        }
                    },
                    error: function(res, textStatus, errorThrown){
                        // console.log(res);
                        check_transaction_status_updateTips(ajax_ele,res.status + ' ' + res.statusText,'ajax-no-match','color:#f00');
                    }
                });
            }

            //please keep following selector as same as above: var result_area=$(this);//
            $("#check_transaction_status").on('check_transaction_status_received',function(evt,res){
                // console.log(res);
                // $(res.data).insertAfter(".ajax-loading");
            });

            function check_transaction_status_updateTips(lead_ele,ele_msg,ele_class,ele_style){
                ele_tips=$(lead_ele).parent().find('.check_transaction_status_msg-tips');
                if ($(ele_tips).length > 0) {
                    $(ele_tips).html(ele_msg);
                    $(ele_tips).attr('style',ele_style);
                }else{
                    $(lead_ele).after('<span class="check_transaction_status_msg-tips" class="' + ele_class + '" style="'+ ele_style +'">' + ele_msg +'</span>');
                }
                // setTimeout(function() {
                //   $(ele_tips).html('&nbsp;');//to avoid jump
                // }, 5000);        
            }
      });
</script>

