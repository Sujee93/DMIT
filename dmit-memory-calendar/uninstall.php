<?php
/**
 * Fires only on "Delete" from the Plugins screen (not on deactivate).
 * Removes plugin settings; deliberately leaves History Event posts,
 * categories and uploaded images in place since deleting a visitor's
 * historical content without an explicit opt-in would be destructive.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'dmit_mc_settings' );
