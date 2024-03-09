<?php

/**
 * Writes a message to the plugin's log file.
 *
 * @param string $message The message to log.
 * @param int $error_level Optional error level for the message (defaults to E_USER_NOTICE).
 */
function wp_wc_pve_write_log($message, $error_level = E_USER_NOTICE) {
  $log_dir = WP_CONTENT_DIR . '/uploads/pve-logs/';

  // Check if the directory exists, if not create it.
  if (!is_dir($log_dir)) {
    wp_mkdir_p($log_dir);
  }

  $filename = date('Y-m-d') . '.log';
  $log_file = $log_dir . $filename;

  $message = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;

  // Use error_log to write the message to the log file with the specified error level.
  error_log($message, 3, $log_file);
}

