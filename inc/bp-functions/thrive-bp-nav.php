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
    if ( empty( $count ) || is_null ( $count ) ) {
        $count = 0;
    }
    $class = ( 0 === $count ) ? 'no-count' : 'count';

    $markup = '<span class="' . $class . '">' . $count . '</span>';

    return $markup;
}

/**
* Fetch the Primary BP Menus.
*
* @since 2.2.0
*
* @return array $bp_filtered_components_menu List of BP primary menus.
*/
function thrive_bp_component_nav_setup() {
    if ( !function_exists('buddypress') ) {
        return;
    }

    $bp = buddypress();
    $logged_in_id = bp_loggedin_user_id();
    $bp_active_components = $bp->active_components;
    $bp_active_components_loaded = $bp->loaded_components;
    $bp_components_menu = array();
    $bp_filtered_components_menu = array();
    $component_name = '';

    foreach ( $bp_active_components_loaded as $component => $value ) {
        $component_name = $bp->bp_nav[$component]['name'];
        $bp->bp_nav[$component]['component_name'] = $component;

        if ( 'messages' === $component ) {
            $user_total_messages = BP_Messages_Thread::get_inbox_count( $logged_in_id );
            $bp->bp_nav[$component]['component_notifications'] = thrive_component_notification_wrapper( $user_total_messages );
        }
        if ( 'notifications' === $component ) {
            $user_total_notifications = thrive_component_notification_wrapper( bp_notifications_get_unread_notification_count( $logged_in_id ) );
            $bp->bp_nav[$component]['component_notifications'] = $user_total_notifications;
        }
        if ( 'friends' === $component ) {
            $user_total_friends = thrive_component_notification_wrapper( friends_get_total_friend_count( $logged_in_id ) );
            $bp->bp_nav[$component]['component_notifications'] = $user_total_friends;
        }
        if ( 'groups' === $component ) {
            $user_total_groups = thrive_component_notification_wrapper( groups_total_groups_for_user( $logged_in_id ) );
            $bp->bp_nav[$component]['component_notifications'] = $user_total_groups;
        }
        if ( 'projects' === $component ) {
            if( class_exists( 'TaskBreakerCore' ) ) {
                $taskbreaker = new TaskBreakerCore();
                $taskbreaker_user_total_projects = $taskbreaker->get_user_groups_projects( $logged_in_id );

                $bp->bp_nav[$component]['component_notifications'] = thrive_component_notification_wrapper( $taskbreaker_user_total_projects['total'] );
            }
        }
        $bp->bp_nav[$component]['component_label'] = preg_replace("#(<span.*?>)(.*?)(</span>)#", "", $component_name) . $bp->bp_nav[$component]['component_notifications'];

        if ( ! is_null( $bp->bp_nav[$component] ) ) {
            $bp_components_menu[] = $bp->bp_nav[$component];
        }
    }
    foreach ( $bp_components_menu as $menu) {
        if( true === $menu['primary'] ) {
            $bp_filtered_components_menu[] = $menu;
        }
    }

    thrive_array_sort_by_column( $bp_filtered_components_menu, 'position' );

    return $bp_filtered_components_menu;
}

/**
* Fetch the Secondary BP Menus.
*
* @since 2.2.0
*
* @return void List of BP secondary menus markup.
*/
function thrive_bp_get_sub_nav( $parent_slug = '' ) {
    if ( !function_exists('buddypress') ) {
        return;
    }

    $bp = buddypress();
    $sub_nav = array();
    $class = '';

    // If we are looking at a member profile, then the we can use the current
    // component as an index. Otherwise we need to use the component's root_slug.
    $component_index = !empty( bp_loggedin_user_id() ) ? bp_current_component() : bp_get_root_slug( bp_current_component() );
    $selected_item = bp_current_action();

    // Default to the Members nav.
    if ( ! bp_is_single_item() ) {
        // Set the parent slug, if not provided.
        if ( empty( $parent_slug ) ) {
            $parent_slug = $component_index;
        }

        $secondary_nav_items = $bp->members->nav->get_secondary( array( 'parent_slug' => $parent_slug ) );

        if ( ! $secondary_nav_items ) {
            return false;
        }

    // For a single item, try to use the component's nav.
    } else {
        $current_item = bp_current_item();
        $single_item_component = bp_current_component();

        // Adjust the selected nav item for the current single item if needed.
        if ( ! empty( $parent_slug ) ) {
            $current_item = $parent_slug;
            $selected_item = bp_action_variable( 0 );
        }

        // If the nav is not defined by the parent component, look in the Members nav.
        if ( ! isset( $bp->{$single_item_component}->nav ) ) {
            $secondary_nav_items = $bp->members->nav->get_secondary( array( 'parent_slug' => $current_item ) );
        } else {
            $secondary_nav_items = $bp->{$single_item_component}->nav->get_secondary( array( 'parent_slug' => $current_item ) );
        }

        if ( ! $secondary_nav_items ) {
            return false;
        }
    }

    // Loop through each navigation item.
    foreach ( $secondary_nav_items as $subnav_item ) {

        $class = '';

        // If the current action or an action variable matches the nav item id, then add a highlight CSS class.
        if ( $selected_item == $subnav_item->slug ) {
            $class .= 'current-menu-item';
        } else {
            $class .= '';
        }

        $sub_nav['name'] = $subnav_item->name;
        $sub_nav['slug'] = $subnav_item->slug;
        $sub_nav['parent'] = $subnav_item->parent_slug;
        $sub_nav['css_id'] = $subnav_item->css_id;
        $sub_nav['class'] = $class;

        $sub_nav['link'] = bp_loggedin_user_domain() . $sub_nav['parent'] . '/' . $sub_nav['slug'];

        ?>
        <li id="<?php echo esc_attr( $sub_nav['css_id'] ) ?>" class="menu-child <?php echo esc_attr( $sub_nav['class'] ) ?>">
            <a href="<?php echo esc_url( $sub_nav['link'] ) ?>">
                <?php echo esc_html( $sub_nav['name'] ) ?>
            </a>
        </li>
        <?php
    }

    return;
}







/**
* Fetch the Primary BP Menus.
*
* @since 2.2.0
*
* @return array $bp_filtered_components_menu List of BP primary menus.
*/
function thrive_bp_get_nav() {
    if ( !function_exists('buddypress') ) {
        return;
    }

    $bp = buddypress();
    $logged_in_id = bp_loggedin_user_id();
    $navigations = bp_nav_menu_get_loggedin_pages();
    $current_component = bp_current_component();
    $filtered_navigations = array();
    $subnavigations = array();
    $nav_index = 0;
    $has_subnav = '';
    $class = '';
    $notification_count = '';

    foreach( $navigations as $navigation => $nav_value ) {
        $slug = $nav_value->post_excerpt;
        $subnavigations = thrive_bp_get_subnav( $slug );

        if( bp_nav_item_has_subnav( $slug ) ) {
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
            $user_total_messages = BP_Messages_Thread::get_inbox_count( $logged_in_id );
            $notification_count = thrive_component_notification_wrapper( $user_total_messages );
        } elseif ( 'notifications' === $slug ) {
            $user_total_notifications = thrive_component_notification_wrapper( bp_notifications_get_unread_notification_count( $logged_in_id ) );
            $notification_count = $user_total_notifications;
        } elseif ( 'friends' === $slug ) {
            $user_total_friends = thrive_component_notification_wrapper( friends_get_total_friend_count( $logged_in_id ) );
            $notification_count = $user_total_friends;
        } elseif ( 'groups' === $slug ) {
            $user_total_groups = thrive_component_notification_wrapper( groups_total_groups_for_user( $logged_in_id ) );
            $notification_count = $user_total_groups;
        } elseif ( 'projects' === $slug ) {
            if( class_exists( 'TaskBreakerCore' ) ) {
                $taskbreaker = new TaskBreakerCore();
                $taskbreaker_user_total_projects = $taskbreaker->get_user_groups_projects( $logged_in_id );

                $notification_count = thrive_component_notification_wrapper( $taskbreaker_user_total_projects['total'] );
            }
        } else {
            $notification_count = '';
        }

        $filtered_navigations[$nav_index] = array(
            'name' => $nav_value->post_title,
            'slug' => $slug,
            'link' => $nav_value->guid,
            'css_id' => $slug,
            'class' => $class,
            'count' => $notification_count,
            'primary' => true,
            'has_subnav' => $has_subnav,
        );

        if( bp_nav_item_has_subnav( $slug ) ) {
            $filtered_navigations[$nav_index]['subnav'] = $subnavigations;
        }

        $nav_index++;
    }

    return $filtered_navigations;
}

/**
* Fetch the Secondary BP Menus.
*
* @since 2.2.0
*
* @return void List of BP secondary menus markup.
*/
function thrive_bp_get_subnav( $parent_slug = '' ) {
    if ( !function_exists('buddypress') ) {
        return;
    }

    $bp = buddypress();
    $sub_nav = array();
    $filtered_subnav = array();
    $class = '';

    // If we are looking at a member profile, then the we can use the current
    // component as an index. Otherwise we need to use the component's root_slug.
    $component_index = !empty( bp_loggedin_user_id() ) ? bp_current_component() : bp_get_root_slug( bp_current_component() );
    $selected_item = bp_current_action();

    // Default to the Members nav.
    if ( ! bp_is_single_item() ) {
        // Set the parent slug, if not provided.
        if ( empty( $parent_slug ) ) {
            $parent_slug = $component_index;
        }

        $secondary_nav_items = $bp->members->nav->get_secondary( array( 'parent_slug' => $parent_slug ) );

        if ( ! $secondary_nav_items ) {
            return false;
        }

    // For a single item, try to use the component's nav.
    } else {
        $current_item = bp_current_item();
        $single_item_component = bp_current_component();

        // Adjust the selected nav item for the current single item if needed.
        if ( ! empty( $parent_slug ) ) {
            $current_item = $parent_slug;
            $selected_item = bp_action_variable( 0 );
        }

        // If the nav is not defined by the parent component, look in the Members nav.
        if ( ! isset( $bp->{$single_item_component}->nav ) ) {
            $secondary_nav_items = $bp->members->nav->get_secondary( array( 'parent_slug' => $current_item ) );
        } else {
            $secondary_nav_items = $bp->{$single_item_component}->nav->get_secondary( array( 'parent_slug' => $current_item ) );
        }

        if ( ! $secondary_nav_items ) {
            return false;
        }
    }

    // Loop through each navigation item.
    foreach ( $secondary_nav_items as $subnav_item ) {

        $class = '';

        // If the current action or an action variable matches the nav item id, then add a highlight CSS class.
        if ( $selected_item == $subnav_item->slug ) {
            $class .= 'menu-child current-menu-item';
        } else {
            $class .= 'menu-child';
        }

        $sub_nav['name'] = $subnav_item->name;
        $sub_nav['slug'] = $subnav_item->slug;
        $sub_nav['parent'] = $subnav_item->parent_slug;
        $sub_nav['css_id'] = $subnav_item->css_id;
        $sub_nav['class'] = $class;

        $sub_nav['link'] = bp_loggedin_user_domain() . $sub_nav['parent'] . '/' . $sub_nav['slug'];
        $sub_nav['user_has_access'] = $subnav_item->user_has_access;
        $sub_nav['secondary'] = true;
        $filtered_subnav[] = $sub_nav;
    }

    return $filtered_subnav;
}


function __pre( $content ) {
    echo '<pre>';
        print_r( $content );
    echo '</pre>';
}
add_action( 'the_content', 'the_display' );

function the_display() {
    __pre(thrive_bp_get_nav());
}
