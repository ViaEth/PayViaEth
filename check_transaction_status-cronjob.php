<?php
// This function adds custom cron schedules to the existing WordPress cron schedules.
// It accepts an array of existing cron schedules and returns the same array with new custom schedules.
function c9wep_add_check_transaction_status_cron_schedules($schedules) {
  // Adds a new custom schedule for 3 minutes interval.
  $schedules['3_minutes'] = array(
    'interval' => 60*3,
    'display' => __('3 Minutes')
  );
  // Adds a new custom schedule for 5 minutes interval.
  $schedules['5_minutes'] = array(
    'interval' => 60*5,
    'display' => __('5 Minutes')
  );
  // Adds a new custom schedule for 8 minutes interval.
  $schedules['8_minutes'] = array(
    'interval' => 60*8,
    'display' => __('8 Minutes')
  );
  // Adds a new custom schedule for 10 minutes interval.
  $schedules['10_minutes'] = array(
    'interval' => 60*10,
    'display' => __('10 Minutes')
  );
// Returns the updated array of schedules.
return $schedules;
}
// Adds the custom cron schedules to WordPress cron schedules.
add_filter('cron_schedules', 'c9wep_add_check_transaction_status_cron_schedules');

// This function sets up a cron job for the provided hook and schedule ID.
// If the provided schedule ID is not valid, it sets the schedule ID to "hourly" by default.
// If a previous cron job is already scheduled for the provided hook, it clears it.
function c9wep_setup_check_transaction_status_cron_job($cron_hook,$schedule_id='hourly') {
    // An array of all available schedules, including custom ones.
    $all_schedules_ids=array('3_minutes','5_minutes','8_minutes','10_minutes','hourly','daily','twicedaily');
    // If the provided schedule ID is not valid, it sets the schedule ID to "hourly" by default.
    if(false==in_array($schedule_id,$all_schedules_ids)){
        $schedule_id='hourly';
    }
    // Clears the previous scheduled cron job, if any.
    if (wp_next_scheduled( $cron_hook )) {//clear previous settings
        wp_clear_scheduled_hook($cron_hook);
    }
    // Schedules a new cron job for the provided hook and schedule ID.
    wp_schedule_event(time(), $schedule_id, $cron_hook);
}

// This function is the actual task that is executed when the cron job is run.
// It calls another function to update transaction status for all orders.
function c9wep_check_transaction_status_cron_task() {
    c9wep_update_transactions_status_all_orders();
}
// Adds the cron job hook and the task to execute when the cron job is run.
add_action( 'c9wep_check_transaction_status_cron_hook', 'c9wep_check_transaction_status_cron_task' );

