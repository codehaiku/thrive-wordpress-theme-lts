<?php
/**
* Rearrange the array base on the parameter given.
*
* @since 2.2.0
*
* @return array Rearrange array.
*/
function thrive_array_sort_by_column( &$arr, $col, $dir = SORT_ASC ) {
    $sort_col = array();

    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }

    return array_multisort($sort_col, $dir, $arr);
}

/**
* Wrapper for the BP component count.
*
* @since 2.2.0
*
* @return string HTML count wrapper.
*/
function thrive_component_notification_wrapper( $count ) {

    $class = '';
    $markup = '';

    if ( empty( $count ) || is_null ( $count ) ) 
    {
        $count = 0;
    }

    $class = ( 0 === $count ) ? 'no-count' : 'count';

    $markup = '<span class="' . sanitize_html_class( $class ) . '">' . absint( $count ) . '</span>';

    return $markup;
}

/**
* Fetch the Primary BP Menus.
*
* @since 2.2.0
*
* @return array $bp_filtered_components_menu List of BP primary menus.
*/
function thrive_bp_get_nav() {

    if ( ! function_exists('buddypress') ) {
        return;
    }

    $bp = buddypress();
    $logged_in_id = bp_loggedin_user_id();
    $navigations = bp_nav_menu_get_loggedin_pages();
    $current_component = bp_current_component();
    $filtered_navigations = array();
   
    $nav_index = 0;
    $has_subnav = '';
    $class = '';
    $notification_count = '';

    foreach( $navigations as $navigation => $nav_value ) {

        $slug = $nav_value->post_excerpt;

        if( bp_nav_item_has_subnav( $slug ) ) 
        {
            $has_subnav = true;
        } else {
            $has_subnav = false;
        }

        if ( $current_component == $slug ) {
            $class = 'menu-parent current-menu-item';
        } else {
            $class = 'menu-parent';
        }

        if ( 'messages' === $slug ) {
            $user_total_messages = BP_Messages_Thread::get_inbox_count( absint( $logged_in_id ) );
            $notification_count = thrive_component_notification_wrapper( absint( $user_total_messages ) );
        } elseif ( 'notifications' === $slug ) {
            $user_total_notifications = thrive_component_notification_wrapper( bp_notifications_get_unread_notification_count( absint( $logged_in_id ) ) );
            $notification_count = $user_total_notifications;
        } elseif ( 'friends' === $slug ) {
            $user_total_friends = thrive_component_notification_wrapper( friends_get_total_friend_count( absint( $logged_in_id ) ) );
            $notification_count = $user_total_friends;
        } elseif ( 'groups' === $slug ) {
            $user_total_groups = thrive_component_notification_wrapper( groups_total_groups_for_user( absint( $logged_in_id ) ) );
            $notification_count = $user_total_groups;
        } elseif ( 'projects' === $slug ) {
            if( class_exists( 'TaskBreakerCore' ) ) {
                $taskbreaker = new TaskBreakerCore();
                $taskbreaker_user_total_projects = $taskbreaker->get_user_groups_projects( absint( $logged_in_id ) );
                $notification_count = thrive_component_notification_wrapper( absint( $taskbreaker_user_total_projects['total'] ) );
            }
        } else {
            $notification_count = '';
        }

        $filtered_navigations[$nav_index] = array(
            'name' => $nav_value->post_title,
            'slug' => sanitize_title( $slug ),
            'link' => $nav_value->guid,
            'css_id' => sanitize_title( $slug ),
            'class' => $class,
            'count' => $notification_count,
            'primary' => true,
            'has_subnav' => $has_subnav,
        );

        $nav_index++;
    }

    return $filtered_navigations;
}
