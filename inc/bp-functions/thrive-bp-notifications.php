<?php
/**
* Fetch the notifications object for the logged-in user.
*
* @since 2.2.0
*
* @return array $notifications List of notifications object for the logged-in user.
*/
function thrive_bp_notifications_get_all_notifications_for_user( $user_id = 0 ) {
    if ( !function_exists('buddypress') ) {
        return;
    }

	// Default to displayed user if no ID is passed.
	if ( empty( $user_id ) ) {
		$user_id = ( bp_displayed_user_id() ) ? bp_displayed_user_id() : bp_loggedin_user_id();
	}

	// Get notifications out of the cache, or query if necessary.
	$notifications = wp_cache_get( 'all_for_user_' . $user_id, 'bp_notifications' );
	if ( false === $notifications ) {
		$notifications = BP_Notifications_Notification::get( array(
			'user_id' => $user_id
		) );
		wp_cache_set( 'all_for_user_' . $user_id, $notifications, 'bp_notifications' );
	}

	/**
	 * Filters all notifications for a user.
	 *
	 * @since 2.2.0
	 *
	 * @param array $notifications Array of notifications for user.
	 * @param int   $user_id       ID of the user being fetched.
	 */
	return apply_filters( 'thrive_bp_notifications_get_all_notifications_for_user', $notifications, $user_id );
}

/**
* Fetch the notifications for the logged-in user.
*
* @since 2.2.0
*
* @return array $descriptions List of notifications for the logged-in user.
*/
function thrive_bp_get_the_notifications_description() {
    if ( !function_exists('buddypress') ) {
        return;
    }

    $bp = buddypress();
    $user_id = bp_loggedin_user_id();
    $descriptions = array();

    $notifications = thrive_bp_notifications_get_all_notifications_for_user( $user_id );

    // Callback function exists.
    foreach ( $notifications as $notification) {
        if ( isset( $bp->{ $notification->component_name }->notification_callback ) && is_callable( $bp->{ $notification->component_name }->notification_callback ) ) {
            $description = call_user_func(
                $bp->{ $notification->component_name }->notification_callback,
                $notification->component_action,
                $notification->item_id,
                $notification->secondary_item_id,
                1,
                'string',
                $notification->id
            );

        // @deprecated format_notification_function - 1.5
        } elseif ( isset( $bp->{ $notification->component_name }->format_notification_function ) && function_exists( $bp->{ $notification->component_name }->format_notification_function ) ) {
            $description = call_user_func(
                $bp->{ $notification->component_name }->format_notification_function,
                $notification->component_action,
                $notification->item_id,
                $notification->secondary_item_id,
                1
            );

        // Allow non BuddyPress components to hook in.
        } else {

            /** This filter is documented in bp-notifications/bp-notifications-functions.php */
            $description = apply_filters_ref_array( 'bp_notifications_get_notifications_for_user', array( $notification->component_action, $notification->item_id, $notification->secondary_item_id, 1, 'string', $notification->component_action, $notification->component_name, $notification->id ) );
        }

        $descriptions[] = $description;
    }

    // Reverse the order of the descriptions from latest to oldest.
    $descriptions = array_reverse( $descriptions, true );


    /**
    * Filters the full-text description for a specific notification.
    *
    * @since 2.2.0
    *
    * @param string $descriptions  Full-text description for a specific notification.
    * @param object $notification Notification object.
    */
    return apply_filters( 'thrive_bp_get_the_notifications_description', $descriptions, $notification );
}
