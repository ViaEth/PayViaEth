<?php
function c9wep_add_check_transaction_status_cron_schedules($schedules) {
  $schedules['3_minutes'] = array(
    'interval' => 60*3,
    'display' => __('3 Minutes')
  );
  $schedules['5_minutes'] = array(
    'interval' => 60*5,
    'display' => __('5 Minutes')
  );
  $schedules['8_minutes'] = array(
    'interval' => 60*8,
    'display' => __('8 Minutes')
  );
  $schedules['10_minutes'] = array(
    'interval' => 60*10,
    'display' => __('10 Minutes')
  );
return $schedules;
}
add_filter('cron_schedules', 'c9wep_add_check_transaction_status_cron_schedules');

function c9wep_setup_check_transaction_status_cron_job($cron_hook,$schedule_id='hourly') {
    $all_schedules_ids=array('3_minutes','5_minutes','8_minutes','10_minutes','hourly','daily','twicedaily');
    if(false==in_array($schedule_id,$all_schedules_ids)){
        $schedule_id='hourly';
    }
    if (wp_next_scheduled( $cron_hook )) {//clear previous settings
        wp_clear_scheduled_hook($cron_hook);
    }
    wp_schedule_event(time(), $schedule_id, $cron_hook);
}

function c9wep_check_transaction_status_cron_task() {
    c9wep_update_transactions_status_all_orders();
}
add_action( 'c9wep_check_transaction_status_cron_hook', 'c9wep_check_transaction_status_cron_task' );

