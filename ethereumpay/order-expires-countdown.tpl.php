<?php 
function c9wep_get_gmt_offset(){
  //https://stackoverflow.com/questions/33384693/get-gmt-offset-from-gmt-offset-option-in-wordpress
  $min    = 60 * get_option('gmt_offset');
  $sign   = $min < 0 ? "-" : "+";
  $absmin = abs($min);
  $tz     = sprintf(" GMT%s%02d:%02d", $sign, $absmin/60, $absmin%60);

  return $tz;
}
$order_created_time=c9wep_get_order_created_time($order_id) . c9wep_get_gmt_offset();//' GMT+08:00';
$order_expired_time=$metas[PAYMENT_EXPIRED_TIME] . c9wep_get_gmt_offset();//' GMT+08:00';//c9wep_get_payment_expired_time($order_id);
// ob_start();
// print_r($order_created_time);
// echo PHP_EOL;
// print_r($order_expired_time);
// echo PHP_EOL;
// echo PHP_EOL;
// echo PHP_EOL;
// echo PHP_EOL;
// $data1=ob_get_clean();
// file_put_contents(dirname(__FILE__)  . '/order_created_time.log',$data1,FILE_APPEND);
?>
<div class="row timeleft-countdown-row-wrapper">
    <div class="col col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 timeleft_countdown-col-wrapper">
        <div class="timeleft_countdown-inner">
          <div id="clockdiv">
            <div>
              You have <span class="time minutes-left"></span> minutes to pay for this order. <br/>Your order will expire at <span id="order-expired" class="time order-expired"></span>
            </div>
            <div class="progress">
              <div class="progress-bar progress-bar-striped active"></div>
            </div>
            <?php if(false): ?>
            <?php endif;//end false ?>
          </div>
        </div> <!-- timeleft_countdown-inner -->
    </div> <!-- timeleft_countdown-col-wrapper-->
</div> <!-- row timeleft-countdown-row-wrapper-->

<style>
  .timeleft_countdown-inner{

  } /*.timeleft_countdown-inner*/
</style>
<style type="text/css">
/*@keyframes progress-bar-stripes {
  from {
    background-position: 1rem 0; }
  to {
    background-position: 0 0; } }

.bst4-wrapper .progress {
  display: flex;
  height: 1rem;
  overflow: hidden;
  font-size: 0.75rem;
  background-color: #e9ecef;
  border-radius: 0.25rem; }

.bst4-wrapper .progress-bar {
  display: flex;
  flex-direction: column;
  justify-content: center;
  color: #fff;
  text-align: center;
  white-space: nowrap;
  background-color: #007bff;
  transition: width 0.6s ease; }
  @media screen and (prefers-reduced-motion: reduce) {
    .bst4-wrapper .progress-bar {
      transition: none; } }

.bst4-wrapper .progress-bar-striped {
  background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
  background-size: 1rem 1rem; }

.bst4-wrapper .progress-bar-animated {
  animation: progress-bar-stripes 1s linear infinite; }*/

/*#clockdiv {
  font-weight: 600;
  text-transform: uppercase;
}*/
#clockdiv {
  font-size: 80%;
  border: 1px solid #eee;
  background: #eee;
  padding: 5px;
  /*border-radius: 5px;*/
  margin-top: 5px;
  /*font-size: 12px;*/
}

#clockdiv .time{
  font-weight: bold;
}
/*.progress {
  margin-bottom: 8px;
  border-radius: 4px;
}
.progress-bar {
  width: 100%;
  border-radius: 4px;
}*/
</style>

<script type="text/javascript">
function Clock(id, startDate, endDate) {
  this.clock = document.getElementById(id);
  this.timeEl = this.clock.querySelector('.time');
  this.progressEl = this.clock.querySelector('.progress-bar');
  this.startDate = startDate;
  this.endDate = endDate;
  
  this.updateClock();
  this.interval = setInterval(this.updateClock.bind(this), 1000);
}
   
Clock.prototype.updateClock = function() {
  var rem = this.getTimeRemaining();
  
  // Update time element
  var duration = [
    // rem.days,
    // this.padLeft(rem.hours),
    rem.minutes,
    this.padLeft(rem.seconds)
  ];
  this.timeEl.innerHTML = duration.join(":");
  
  // Update progress
  var progress = this.getProgress(rem.total);
  this.progressEl.style.width = (progress * 100) + "%";
  
  // Clear intervall when done
  if(rem.t === 0 && this.interval) {
    clearInterval(this.interval);
    delete this.interval;
  }
};

Clock.prototype.padLeft = function(number) {
  return ('0' + number).slice(-2);
};

Clock.prototype.getTimeRemaining = function() {
  var t = this.endDate - new Date();
  if(t < 0) t = 0;

  return {
    total: t,
    days: Math.floor(t / (1000 * 60 * 60 * 24)),
    hours: Math.floor((t / (1000 * 60 * 60)) % 24),
    minutes: Math.floor((t / 1000 / 60) % 60),
    seconds: Math.floor((t / 1000) % 60)
  };
};

Clock.prototype.getProgress = function(remainingTime) {
  var totalTime = this.endDate - this.startDate;
  return 1 - (remainingTime / totalTime);
};
</script>

<script type="text/javascript">
  // Example. Replace startDate and endDate with your dates (30sec for demo)
  var startDate = new Date('<?php echo $order_created_time; ?>');
  var endDate = new Date('<?php echo $order_expired_time; ?>');

  document.getElementById('order-expired').innerHTML = endDate.toLocaleString();
  console.log('startDate:');
  console.log(startDate);
  console.log('endDate:');
  console.log(endDate);
  new Clock("clockdiv", startDate, endDate);
</script>