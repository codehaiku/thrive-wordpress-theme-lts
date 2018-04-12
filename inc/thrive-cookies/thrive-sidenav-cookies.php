<?php
/**
 * Added SideNav Sidebar state functionality
 * to save the last state of the SideNav Sidebar.
 */
add_action( 'init', 'thrive_set_sidenav_cookie' );

function thrive_set_sidenav_cookie() {
    $sidenav_state = '';
    $sidenav_cookie = '';
    $filtered_sidenav_cookie = '';
    $set_sidenav_cookie = '';

    if ( empty( $_COOKIE['isSideNavSidebarClose'] ) ) {
        $_COOKIE['isSideNavSidebarClose'] = apply_filters( 'thrive_set_default_sidenav_state', __return_false() );
    }

    $sidenav_cookie = $_COOKIE['isSideNavSidebarClose'];

    $filtered_sidenav_cookie = filter_var( $sidenav_cookie, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

    $set_sidenav_cookie = apply_filters( 'thrive_set_sidenav_state', $filtered_sidenav_cookie );

    setcookie( 'isSideNavSidebarClose', $set_sidenav_cookie, time() + (86400 * 30), "/" );
    return;
}

function thrive_get_sidenav_state() {
    $sidenav_state = '';
    $sidenav_cookie = '';
    $filtered_sidenav_cookie = '';

    if ( empty( $_COOKIE['isSideNavSidebarClose'] ) ) {
        $_COOKIE['isSideNavSidebarClose'] = apply_filters( 'thrive_get_default_sidenav_state', __return_false() );
    }

    $sidenav_cookie = $_COOKIE['isSideNavSidebarClose'];

    $filtered_sidenav_cookie = filter_var( $sidenav_cookie, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

    if ( true === $filtered_sidenav_cookie ) {
        $sidenav_state = 'toggled';
    }

    return apply_filters( 'thrive_get_sidenav_state', $sidenav_state );
}