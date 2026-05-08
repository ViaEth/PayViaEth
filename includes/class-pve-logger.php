<?php
/**
 * PVE_Logger — Plugin logging helper.
 *
 * Writes plugin-specific log entries to wp-content/uploads/pve-logs/.
 * Uses WP_Filesystem for file operations and gmdate() for timestamps
 * to avoid timezone-related date drift. Replaces the legacy
 * wp_wc_pve_logging.php helper from earlier alpha builds.
 *
 * Hooks registered:
 *   none
 *
 * Options read:
 *   none
 *
 * Options written:
 *   none
 *
 * Order meta read:
 *   none
 *
 * Order meta written:
 *   none
 *
 * Constants defined:
 *   none
 *
 * Files loaded:
 *   wp-admin/includes/file.php (lazy-loaded for WP_Filesystem)
 *
 * @package Payments_Via_Ethereum
 * @since   1.420.69
 */

//ABSPATH guard, must be first executable line, no exceptions
defined( 'ABSPATH' ) || exit;

class PVE_Logger {

	/**
	 * Write a log entry to the PVE log file.
	 *
	 * @since 1.420.69
	 * @param string $message The message to log.
	 * @return bool True on success, false on failure.
	 */
	public static function log( $message ) {
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}

		$upload_dir = wp_upload_dir();
		$log_dir    = trailingslashit( $upload_dir['basedir'] ) . 'pve-logs';
		$log_file   = trailingslashit( $log_dir ) . 'pve.log';

		if ( ! $wp_filesystem->is_dir( $log_dir ) ) {
			$wp_filesystem->mkdir( $log_dir, FS_CHMOD_DIR );
		}

		$timestamp = gmdate( 'Y-m-d H:i:s' );
		$entry     = sprintf( "[%s UTC] %s\n", $timestamp, $message );

		$existing = $wp_filesystem->exists( $log_file ) ? $wp_filesystem->get_contents( $log_file ) : '';
		return $wp_filesystem->put_contents( $log_file, $existing . $entry, FS_CHMOD_FILE );
	}
}

/**
 * Convenience wrapper for PVE_Logger::log().
 *
 * @since 1.420.69
 * @param string $message The message to log.
 * @return bool True on success, false on failure.
 */
function pve_log( $message ) {
	return PVE_Logger::log( $message );
}

