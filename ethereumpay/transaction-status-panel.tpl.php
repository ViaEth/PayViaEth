<?php 
$interval=c9wep_get_checking_interval_by_order_id($order_id);
?>
<div class="row transaction-status-row-wrapper">
    <div class="col col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 transaction-status-col-wrapper">
        <div class="transaction-status-inner">
          <?php if(false): ?>
            Status: <span id="tr-status"> N/A </span>
            <br/>
          <?php endif;//end false ?>
            <a href="javascript:void(0);" id="refresh-status" data-order_id="<?php echo $order_id; ?>" class="button button-default btn btn-primary">Check Status</a><span id="check-countdown"></span><br/>
            <div id="status-result"></div>
        </div> <!-- transaction-status-inner -->
    </div> <!-- transaction-status-col-wrapper-->
</div> <!-- row transaction-status-row-wrapper-->

<style>
  .transaction-status-inner{

  } /*.transaction-status-inner*/
  #refresh-status{
    margin-right: 10px;
  }
</style>

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

