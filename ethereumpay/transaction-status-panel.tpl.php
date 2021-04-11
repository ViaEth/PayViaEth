<?php 
$interval=c9wep_get_checking_interval_by_order_id($order_id);
$order_status=c9wep_get_order_status($order_id);
?>
<div class="row transaction-status-row-wrapper">
    <div class="col col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 transaction-status-col-wrapper">
        <div class="transaction-status-inner">
            Order Status: <span id="tr-status"><?php echo $order_status; ?> </span>
            <a href="javascript:void(0);" id="refresh-status" data-order_id="<?php echo $order_id; ?>" class="button button-default btn btn-primary">Refresh</a><span id="check-countdown"></span><span id="status-result"></span><br/>
            <span class="bookmark-notice">Press <b>Ctrl + D</b> to Bookmark this page</span>
        </div> <!-- transaction-status-inner -->
    </div> <!-- transaction-status-col-wrapper-->
</div> <!-- row transaction-status-row-wrapper-->

<style>
  .bookmark-notice{
    font-size: 70%;
    font-style: italic;
  }

  .transaction-status-inner{
    margin: 10px 0;
  } /*.transaction-status-inner*/

  #tr-status{
    font-weight: bold;
  }

  #refresh-status {
    margin-right: 10px;
    padding: 5px;
    text-transform: none !important;
    border-radius: 5px;
    margin-left: 10px;
  }
</style>
<?php if(false): ?>
  
<script type="text/javascript">
  jQuery(function($){
    countdown();//init count
    $("#status-result").on('ft_check_transaction_status_received',function(evt,res){
        // console.log(res);
        // $(res.data).insertAfter(".ajax-loading");
        countdown();
    });

    function countdown(){
      var timeleft = <?php echo $interval; ?>;
      var downloadTimer = setInterval(function(){
        if(timeleft <= 0){
          clearInterval(downloadTimer);
          document.getElementById("check-countdown").innerHTML = "";
          $('#refresh-status').click();//trigger again
        } else {
          document.getElementById("check-countdown").innerHTML = 'in ' + timeleft + " seconds";
        }
        timeleft -= 1;
      }, 1000);
    }
  });
</script>

<?php endif;//end false ?>
